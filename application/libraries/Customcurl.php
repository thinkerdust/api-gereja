<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customcurl
{
  private $authorization = 'AAAAx85tqkc:APA91bG8-WRYqqHE8awcU1aQMPeffaZQr4FJocVSjFX_6pBAWW30hy82IzpthISyo2AHzkuHCZDQWPgoJ40IrogeafSuqflfZiBsR8w0m_0gte0ZT189jrNfxfElN32W4XexlX9O56_u';

  function fcm($flag = '', $token = '', $title = '', $body = '', $image = '', $data = '')
  {
      $curl = curl_init();

      $ci =& get_instance();

      $json = array(
        'to' => ($token) ? $token : 'fJGd7XQCLkSFjfS3zVqoYb:APA91bHWLRakVWYtYI2JIOnpN8-M9bZw2kdtMXkpHxkGbjoQFIa3jjKMdl8WETDSj13CJStBNuL_TkzhNM5zGeSfWmuiw6-gntlEjjeyx1CNeu2GOIEVfnRpQCZFPUNvrsR_wt0t1YBC',
        'notification' => [
            'title' => $title,
            'body'=> $body,
            'image' => $image,
            'sound' => 'default',
            'android_channel_id' => 'Helasterion'
        ],
        'data'=> ($data) ? $data : null,
        'priority'=> 'high'
      );

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($json),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Authorization: key='.$this->authorization
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $decode = json_decode($response,true);

      if($decode['success'] == 1){
        $message_id = $decode['results'][0]['message_id'];
        $ins = $ci->db->insert('log_notifikasi', [
                    'token' => $token, 
                    'nij' => nij($token),
                    'message_id'=> $message_id,
                    'title' => $title,
                    'body'=> $body,
                    'flag' => $flag
                ]);
      }
      return $decode;
  }
}