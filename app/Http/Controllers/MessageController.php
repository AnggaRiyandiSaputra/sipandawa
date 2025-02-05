<?php

namespace App\Http\Controllers;

use App\Models\MessageHistories;
use Carbon\Carbon;
use App\Models\Messages;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class MessageController extends Controller
{
    public function sendMessage($id)
    {
        $message = Messages::find($id);
        $wrongNumber = '99';
        $phone1 = '085135424767';
        $pjPhone = $message->Employee->phone;
        $clientPhone = $message->Client->phone;
        $messageContent = $message->message;
        $schedule = $message->schedule
            ? Carbon::createFromFormat('Y-m-d H:i:s', $message->schedule, 'Asia/Jakarta')->timestamp
            : null;

        if ($schedule != null) {
            //jika ada schedule

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $pjPhone . ',' . $phone1,
                    'message' => $messageContent,
                    'schedule' => $schedule,
                    'countryCode' => '62', //optional
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 1#LfbQWhjxi6bZvtwb6B' //change TOKEN to your actual token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // Decode JSON ke array
            $data = json_decode($response, true);

            // Mengambil nilai target dan status
            $statusMessage = $data['status'];
            if ($statusMessage == false) {
                $reason = $data['reason'];
                Notification::make()
                    ->title('Pesan gagal terkirim, ' . $reason)
                    ->danger()
                    ->send();

                //kirim data ke tavel history message

                return redirect()->back();
            } else {
                $targerMessage = $data['target'];
                Notification::make()
                    ->title('Pesan akan terkirim ke nomor ' . $targerMessage[0] . ' dan ' . $targerMessage[1] . ' pada ' . $message->schedule)
                    ->success()
                    ->send();

                //kirim data ke tavel history message

                return redirect()->back();
            }
        } else {
            //jika tidak ada schedule

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $pjPhone . ',' . $wrongNumber,
                    'message' => $messageContent,
                    'countryCode' => '62', //optional
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 1#LfbQWhjxi6bZvtwb6B' //change TOKEN to your actual token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // Decode JSON ke array
            $data = json_decode($response, true);

            // Mengambil nilai target dan status
            $statusMessage = $data['status'];
            if ($statusMessage == false) {
                $reason = $data['reason'];
                Notification::make()
                    ->title('Pesan gagal terkirim, ' . $reason)
                    ->danger()
                    ->send();

                //kirim data ke tavel history message
                MessageHistories::create([
                    'name_pj' => $message->Employee->name,
                    'name_client' => $message->Client->name,
                    'send_to' => 'message send to ' . $pjPhone . ',' . $wrongNumber,
                    'message' => $messageContent,
                    'status' => 0
                ]);

                return redirect()->back();
            } else {
                $targerMessage = $data['target'];
                Notification::make()
                    ->title('Pesan berhasil terkirim ke nomor ' . $targerMessage[0] . (isset($targerMessage[1]) ? ' dan ' . $targerMessage[1] : ''))
                    ->success()
                    ->send();

                //kirim data ke tavel history message
                MessageHistories::create([
                    'name_pj' => $message->Employee->name,
                    'name_client' => $message->Client->name,
                    'send_to' => 'message send to ' . $pjPhone . ',' . $wrongNumber,
                    'message' => $messageContent,
                    'status' => 1
                ]);
                return redirect()->back();
            }
        }
    }
}
