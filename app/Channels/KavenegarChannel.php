<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class KavenegarChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toKavenegarSMS($notifiable);
        $message = $data['text'];
        $userMobile = $data['mobile'];
        $sender = "sender number";

        try
        {
            $kavenegar = new KavenegarApi('70496368675541647A425645353048522F684878556A6B364B4D7953783349676B5A3131386C5735516E633D');
            $result = $kavenegar->Send($sender, $userMobile, $message);
            if($result) {
                foreach($result as $r){
                    echo "messageid = $r->messageid";
                    echo "message = $r->message";
                    echo "status = $r->status";
                    echo "statustext = $r->statustext";
                    echo "sender = $r->sender";
                    echo "receptor = $r->receptor";
                    echo "date = $r->date";
                    echo "cost = $r->cost";
                }
            }
        }
        catch(\Kavenegar\Exceptions\ApiException $e) {
            // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
            echo $e->errorMessage();
        }
        catch(\Kavenegar\Exceptions\HttpException $e) {
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
            echo $e->errorMessage();
        } catch(\Exceptions $ex) {
            // در صورت بروز خطایی دیگر
            echo $ex->getMessage();
        }
    }
}
