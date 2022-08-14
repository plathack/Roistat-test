<?php

function parserLogs(string $nameFile): string
{
    if (file_exists("./logs/$nameFile")) {

        $logsContent = file_get_contents("./logs/$nameFile");

        return json_encode([
            'views' => parseViews($logsContent),
            'urls' => parseUrls($logsContent),
            'traffic' => parseTraffics($logsContent),
            'crawlers' => parseCrawlers($logsContent, [
                'Google',
                'Bing',
                'Baidu',
                'Yandex'
            ]),
            'statusCodes' => parseStatusCodes($logsContent)
        ]);
    }

    return "Лог-файл не найден";
}

print_r(parserLogs('access_log'));

function parseViews(string $logsContent): int
{
    return count(preg_split('/\n/', $logsContent));
}

function parseUrls(string $logsContent):int
{
    preg_match_all('/\s\/\S*\.php\S*/', $logsContent, $urls);

    return count(array_unique($urls[0]));
}

function parseTraffics(string $logsContent): int
{
    preg_match_all('/\s\d+\s\S*/', $logsContent, $traffics);

    return array_sum(array_map(fn($e)=>(int)substr($e, 5), 
    $traffics[0]));
}

function parseCrawlers(string $logsContent, array $crawlers): array 
{
    $result = [];

    foreach ($crawlers as $crawler) {
        preg_match_all('/'.$crawler.'/', $logsContent, $finedCrawlers);

        $result += [$crawler => count($finedCrawlers[0])];
    }

    return $result;
}

function parseStatusCodes(string $logsContent): array
{
    preg_match_all('/\s[0-9]*\s/', $logsContent, $statusCodes);

    return array_count_values(array_map(fn($e)=>(int)$e, 
    $statusCodes[0]));
}