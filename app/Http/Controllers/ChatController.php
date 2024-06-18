<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use GuzzleHttp\Exception\RequestException;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $userMessage = $request->input('message');
        $client = new \GuzzleHttp\Client();

        // Modelo recomendado pelo OpenAI
        $model = "gpt-3.5-turbo"; // exemplo de modelo atualizado

        try {
            $response = $client->post("https://api.openai.com/v1/chat/{$model}/completions", [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'prompt' => $userMessage,
                    'max_tokens' => 150,
                ],
            ]);

            $botResponse = json_decode($response->getBody()->getContents(), true)['choices'][0]['text'];

            // Armazene a conversa no banco de dados
            $conversation = new \App\Models\Conversation();
            $conversation->user_message = $userMessage;
            $conversation->bot_response = $botResponse;
            $conversation->save();

            // Retorne a resposta do bot
            return response()->json(['message' => $botResponse]);

        } catch (RequestException $e) {
            // Lidar com exceções específicas da requisição, como erros HTTP
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody()->getContents(), true);
                return response()->json(['error' => $error['error']['message'] ?? 'Erro ao processar a solicitação'], 400);
            }

            return response()->json(['error' => 'Erro desconhecido ao processar a solicitação'], 500);
        }
    }
}
