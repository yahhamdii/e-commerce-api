<?php

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sogedial\OAuthBundle\Controller;

use FOS\OAuthServerBundle\Controller\TokenController as BaseTokenController;
use OAuth2\OAuth2;
use OAuth2\OAuth2Exception;
use OAuth2\OAuth2ServerException;
use Sogedial\ApiBundle\Entity\Client;
use Sogedial\ApiBundle\Exception\ForbiddenException;
use Sogedial\OAuthBundle\Entity\Session;
use Sogedial\OAuthBundle\Entity\User;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\EventListener\TokenEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends BaseTokenController
{
    /**
     * @var
     */
    private $em;

    /**
     * TokenController constructor.
     * @param OAuth2 $server
     * @param $em
     */
    public function __construct(OAuth2 $server, $em)
    {
        parent::__construct($server);
        $this->em = $em;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws OAuth2Exception
     */
    public function tokenAction(Request $request)
    {
        try {

            /**
             * [BACK] Blocage client
             * @author: Bardi mohamed chamseddine
             * @JIRA CC-565 <https://oyezcc.atlassian.net/browse/CC-565>
             */
            $user = $this->em->getRepository(User::class)->findOneBy(array("username" => $request->get("username")));
            if ($user instanceof UserCustomer) {

                $client = $user->getClient();

                if ($client instanceof Client) {
                    if(!$this->checkAutorization($client)){
                        throw new ForbiddenException("Not authorized client. Check with your commercial please.");
                    }
                }
            }

            $tokenObject = $this->server->grantAccessToken($request);

            //$this->onDemandToken($request, $tokenObject);

            return $tokenObject;

        } catch (OAuth2ServerException $e) {
            return $e->__toString();
        }
    }

    private function checkAutorization(Client $client)
    {
        $statusClient = $client->getStatus();
        $statusPreorder = $client->getStatusPreorder();
        $statusCatalog = $client->getStatusCatalog();

        //verifier si clientStatus prendra null ou un arrayCollection vide pour soit tester sur is_null ou bien isEmpty of arrayCollection
        $clientStatus = $client->getClientStatus();
        if($statusClient == Client::STATUS_INACTIVE || $clientStatus->isEmpty() || ( $statusPreorder == Client::STATUS_PREORDER_BLOCKED &&  $statusCatalog == Client::STATUS_CATALOG_BLOCKED)){

            return false;
        }

        return true;
    }


    /**
     * @param Event $event
     */
    public function onDemandToken(Request $request, $token)
    {
        /**.
         * Get current user entity
         */
        $user = $this->em->getRepository(User::class)->findOneBy(array('username' => $request->get('username')));
        $session = new Session();
        $attr = $request->attributes->all();
        $server = $request->server->all();
        $browser = get_browser(null, true);


        $userAgent = $this->parseUserAgent($server['HTTP_USER_AGENT']);

        $session->setBrowser($userAgent['browser']);
        $session->setBrowserVersion($userAgent['version']);
        $session->setIp($request->getClientIp());
        $session->setLanguage($request->getLocale());
        $session->setOs($userAgent['platform']);
        $session->setIdSession(uniqid());
        $session->setUser($user);

        $this->em->persist($session);
        $this->em->flush();
    }

    /**
     * @param null $u_agent
     * @return array
     */
    private function parseUserAgent($u_agent = null)
    {
        if (is_null($u_agent)) {
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $u_agent = $_SERVER['HTTP_USER_AGENT'];
            } else {
                throw new \InvalidArgumentException('parseUserAgent requires a user agent');
            }
        }

        $platform = null;
        $browser = null;
        $version = null;

        $empty = array('platform' => $platform, 'browser' => $browser, 'version' => $version);

        if (!$u_agent)
            return $empty;

        if (preg_match('/\((.*?)\)/im', $u_agent, $parent_matches)) {

            preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|iPhone|iPad|Linux|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|Nintendo\ (WiiU?|3DS)|Xbox(\ One)?)
                (?:\ [^;]*)?
                (?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);

            $priority = array('Android', 'Xbox One', 'Xbox');
            $result['platform'] = array_unique($result['platform']);
            if (count($result['platform']) > 1) {
                if ($keys = array_intersect($priority, $result['platform'])) {
                    $platform = reset($keys);
                } else {
                    $platform = $result['platform'][0];
                }
            } elseif (isset($result['platform'][0])) {
                $platform = $result['platform'][0];
            }
        }

        if ($platform == 'linux-gnu') {
            $platform = 'Linux';
        } elseif ($platform == 'CrOS') {
            $platform = 'Chrome OS';
        }

        preg_match_all('%(?P<browser>Camino|Kindle(\ Fire\ Build)?|Firefox|Iceweasel|Safari|MSIE|Trident/.*rv|AppleWebKit|Chrome|IEMobile|Opera|OPR|Silk|Lynx|Midori|Version|Wget|curl|NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
            (?:\)?;?)
            (?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix', $u_agent, $result, PREG_PATTERN_ORDER);


        // If nothing matched, return null (to avoid undefined index errors)
        if (!isset($result['browser'][0]) || !isset($result['version'][0])) {
            return $empty;
        }

        $browser = $result['browser'][0];
        $version = $result['version'][0];

        $find = function ($search, &$key) use ($result) {
            $xkey = array_search(strtolower($search), array_map('strtolower', $result['browser']));
            if ($xkey !== false) {
                $key = $xkey;

                return true;
            }

            return false;
        };

        $key = 0;
        if ($browser == 'Iceweasel') {
            $browser = 'Firefox';
        } elseif ($find('Playstation Vita', $key)) {
            $platform = 'PlayStation Vita';
            $browser = 'Browser';
        } elseif ($find('Kindle Fire Build', $key) || $find('Silk', $key)) {
            $browser = $result['browser'][$key] == 'Silk' ? 'Silk' : 'Kindle';
            $platform = 'Kindle Fire';
            if (!($version = $result['version'][$key]) || !is_numeric($version[0])) {
                $version = $result['version'][array_search('Version', $result['browser'])];
            }
        } elseif ($find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS') {
            $browser = 'NintendoBrowser';
            $version = $result['version'][$key];
        } elseif ($find('Kindle', $key)) {
            $browser = $result['browser'][$key];
            $platform = 'Kindle';
            $version = $result['version'][$key];
        } elseif ($find('OPR', $key)) {
            $browser = 'Opera Next';
            $version = $result['version'][$key];
        } elseif ($find('Opera', $key)) {
            $browser = 'Opera';
            $find('Version', $key);
            $version = $result['version'][$key];
        } elseif ($find('Midori', $key)) {
            $browser = 'Midori';
            $version = $result['version'][$key];
        } elseif ($find('Chrome', $key)) {
            $browser = 'Chrome';
            $version = $result['version'][$key];
        } elseif ($browser == 'AppleWebKit') {
            if (($platform == 'Android' && !($key = 0))) {
                $browser = 'Android Browser';
            } elseif (strpos($platform, 'BB') === 0) {
                $browser = 'BlackBerry Browser';
                $platform = 'BlackBerry';
            } elseif ($platform == 'BlackBerry' || $platform == 'PlayBook') {
                $browser = 'BlackBerry Browser';
            } elseif ($find('Safari', $key)) {
                $browser = 'Safari';
            }

            $find('Version', $key);

            $version = $result['version'][$key];
        } elseif ($browser == 'MSIE' || strpos($browser, 'Trident') !== false) {
            if ($find('IEMobile', $key)) {
                $browser = 'IEMobile';
            } else {
                $browser = 'MSIE';
                $key = 0;
            }
            $version = $result['version'][$key];
        } elseif ($key = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser']))) {
            $key = reset($key);

            $platform = 'PlayStation ' . preg_replace('/[^\d]/i', '', $key);
            $browser = 'NetFront';
        }

        return array('platform' => $platform, 'browser' => $browser, 'version' => $version);
    }

}
