<?php
/**
 * 阿波罗客户端
 * @author      church<wolfqong1993@gmail.com>
 * @since        1.0
 */

namespace Church\ApolloClient;

class Client
{
    private string $configServerUrl = '';

    private string $appId = '';

    private ?string $appSecret = '';

    private string $clusterName = 'default';

    private string $namespace = '';

    private string $clientIp = '';

    private $httpClient = null;

    public function __construct($configServerUrl, $appId, $namespace, $appSecret = null)
    {
        $this->configServerUrl = $configServerUrl;
        $this->appId = $appId;
        $this->namespace = $namespace;
        $this->appSecret = $appSecret;
        $this->httpClient = new \GuzzleHttp\Client();
    }

    public function getFromCache()
    {
        $url = sprintf("%s/configfiles/json/%s/%s/%s", $this->configServerUrl, $this->appId, $this->clusterName, $this->namespace);

        if ($this->clientIp) {
            $url .= '?ip=' . $this->clientIp;
        }

        $currentTimeStamp = Util::currentMills();

        $headers = [];
        if ($this->appSecret) {
            $sign = Util::buildSign($currentTimeStamp, Util::url2PathWithQuery($url), $this->appSecret);

            $headers = [
                'Authorization' => sprintf("Apollo %s:%s", $this->appId, $sign),
                'Timestamp' => $currentTimeStamp
            ];
        }

        $response = $this->httpClient->get($url, ['headers' => $headers]);
        return $response->getBody()->getContents();
    }

    public function getWithoutCache($releaseKey = null, $namespace = null)
    {
        $url = sprintf("%s/configs/%s/%s/%s", $this->configServerUrl, $this->appId, $this->clusterName, $namespace ?? $this->namespace);

        $queryString = [];

        if ($this->clientIp) {
            $queryString['ip'] = $this->clientIp;
        }

        if ($releaseKey) {
            $queryString['releaseKey'] = $releaseKey;
        }

        if (count($queryString) > 0) {
            $url .= '?' . http_build_query($queryString);
        }

        $currentTimeStamp = Util::currentMills();

        $headers = [];
        if ($this->appSecret) {
            $sign = Util::buildSign($currentTimeStamp, Util::url2PathWithQuery($url), $this->appSecret);

            $headers = [
                'Authorization' => sprintf("Apollo %s:%s", $this->appId, $sign),
                'Timestamp' => $currentTimeStamp
            ];
        }

        $response = $this->httpClient->get($url, ['headers' => $headers]);
        return $response->getBody()->getContents();
    }

    public function autoPull($namespaces, callable $callback = null)
    {
        $notifications = [];

        if (! is_array($namespaces)) {
            $namespaces = explode(',', $namespaces);
        }

        foreach ($namespaces as $namespace) {
            $notifications[$namespace] = ['namespaceName' => $namespace, 'notificationId' => -1];
        }

        do {

            $url = sprintf("%s/notifications/v2?appId=%s&cluster=%s&notifications=%s", $this->configServerUrl, $this->appId, $this->clusterName, urlencode(json_encode(array_values($notifications))));

            $currentTimeStamp = Util::currentMills();

            $headers = [];
            if ($this->appSecret) {
                $sign = Util::buildSign($currentTimeStamp, Util::url2PathWithQuery($url), $this->appSecret);

                $headers = [
                    'Authorization' => sprintf("Apollo %s:%s", $this->appId, $sign),
                    'Timestamp' => $currentTimeStamp
                ];
            }

            $response = $this->httpClient->get($url, ['headers' => $headers]);

            if ($response->getStatusCode() == 200) {
                $result = $response->getBody()->getContents();

                $result = json_decode($result, true);

                foreach ($result as $item) {
                    $configuration = $this->getWithoutCache(null, $item['namespaceName']);
                    if (! is_null($callback)) {
                        call_user_func_array($callback, [$configuration]);
                    }
                    $notifications[$item['namespaceName']] = ['namespaceName' => $item['namespaceName'], 'notificationId' => $item['notificationId']];
                }
            }
        } while (true);
    }
}