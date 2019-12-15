<?php

namespace App\Listeners;

use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Interop\Amqp\AmqpTopic;

class AddUser
{

    /** @var int Код успешного выполнения */
    protected const SUCCESS_CODE = 0;

    /** @var int Код при возникновении ошибок */
    protected const FAIL_CODE = 1;

    /**
     * Handle the event.
     *
     * @param string $event
     * @param array $data
     * @return bool|void
     */
    public function handle($event, $data)
    {
        $validator = Validator::make($data, [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100',
            'location' => 'required|string|max:100',
            'action'   => 'required',
        ]);

        if ($validator->fails()) {
             $this->publishAnswer(
                $data['reply_to']['exchange'],
                $data['reply_to']['queue'],
                [
                    'id' => null,
                    'error_code' => static::FAIL_CODE,
                    'error_msg' => (string)$validator->errors(),
                ]
            );
            return static::FAIL_CODE;
        }

        $user = User::store($data['name'], $data['email'], $data['location']);

        $this->publishAnswer(
            $data['reply_to']['exchange'],
            $data['reply_to']['queue'],
            [
                'id' => $user->id,
                'error_code' => static::SUCCESS_CODE,
                'error_msg' => '',
            ]
        );

        return static::SUCCESS_CODE;
    }

    /**
     * @param string $exchange Name of exchange to send
     * @param string $queue Name of queue to send
     * @param array $data
     */
    private function publishAnswer(string $exchange, string $queue, array $data): void
    {
        Config::set('queue.connections.rabbitmq.exchange', $exchange);
        app()->instance(AmqpTopic::class, null);

        publish($queue, $data);
    }

}
