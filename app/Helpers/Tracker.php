<?php
namespace App\Helpers;
class Tracker
{

  public static function ahref($text)
  {
      $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
      if(preg_match($reg_exUrl, $text, $url)) {
       return preg_replace($reg_exUrl, '<a href="'.$url[0].'">'.$url[0].'</a>', $text);
      }
      return $text;
  }

  public static function format($text)
  {
      return self::ahref($text);
  }
  
}