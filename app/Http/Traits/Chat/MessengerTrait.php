<?php

namespace App\Http\Traits\Chat;

use App\Events\Chat\NewMessageSent;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Chats\Room;
use App\Models\Chats\Message;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait MessengerTrait{

    private function sendNotificationToOthers($chatMessage){
        $chatId = $chatMessage->room_id;

        broadcast(new NewMessageSent($chatMessage))->toOthers();
    }

    public function chat_create_message($data){
        //Add Message to Database 
        DB::beginTransaction();

        try{
            $message = Message::create([
                'user_id' => $data['user_id'], 
                'room_id' => $data['room_id'], 
                'content' => $data['content'], 
            ]);

            //Touch the Room to Update statusf
            $chat = Room::find($data['room_id']);
            $chat->touch();

            // Todo send broadcast event to pusher and send notification to onesignal services
            $this->sendNotificationToOthers($message);
            DB::commit();
            return $chat;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
    
}