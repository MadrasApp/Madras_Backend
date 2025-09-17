<?php
if (!function_exists('send_eitaa_message')) {

    /**
     * Send message via Eitaa API
     *
     * @param int $chat_id The chat ID of the user
     * @param string $text The message text to be sent
     * @return mixed The response from the Eitaa API
     */
    function send_eitaa_message($chat_id, $text) {
        $api_url = "https://eitaayar.ir/api/app/sendMessage";
        $token = EITAA_TOKEN; // Replace with your actual token

        // Prepare POST fields
        $post_fields = [
            "token" => $token,
            "chat_id" => $chat_id,
            "text" => $text
        ];

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

        // Execute cURL request and get the response
        $response = curl_exec($ch);

        // Close the cURL session
        curl_close($ch);

        // Return the response as an associative array
        return json_decode($response, true);
    }
}
