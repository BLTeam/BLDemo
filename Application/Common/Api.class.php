<?php
namespace Common;

class Api {

    private $url = 'http://json6.applinzi.com/';

    private $serverTab = array("艾欧尼亚" => "电信一");

    function test() {
        echo "api test" . "<br/>";
    }

    /**
     * @param $summoner
     * @param $serverName
     * @description
     */
    function get_summoner_info($summoner, $serverName) {

        $url = $this->url . 'playerinfo.php?serverName=' . $serverName . '&playerName=' . $summoner;
        $summonerInfo = file_get_contents($url);
        $summonerInfo = json_decode($summonerInfo, true);
        return $summonerInfo;

    }

    /**
     * @param $summoner
     * @param $serverName
     * @description
     */
    function get_summoner_rank($summoner, $serverName) {

        $serverName = $this->serverTab[$serverName];
        $url = $this->url . 's5str.php?serverName=' . $serverName . '&playerName=' . $summoner;
        $summonerRank  = file_get_contents($url);
        $summonerRank = json_decode($summonerRank, true);
        return $summonerRank;

    }

    /**
     * @param $summoner
     * @param $serverName
     * @description
     */
    function get_sommoner_hidden_rank($summoner, $serverName) {

        $serverName = $this->serverTab[$serverName];
        $url = $this->url . 'rank.php?serverName=' . $serverName . '&playerName=' . $summoner;
        $summonerHiddenRank = file_get_contents($url);
        $summonerHiddenRank = json_decode($summonerHiddenRank, true);
        return $summonerHiddenRank;

    }


}