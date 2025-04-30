<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT PHP - Tanya Jawab</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        .chat-box {
            border: 1px solid #ddd;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            text-align: left;
        }
        .user {
            color: blue;
            font-weight: bold;
        }
        .assistant {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>Chat dengan ChatGPT</h2>
    <div class="chat-box" id="chat-box">
        <p><strong>ChatGPT:</strong> Halo! Bagaimana saya bisa membantu Anda hari ini?</p>
    </div>
    
    <input type="text" id="user-input" placeholder="Ketik pertanyaan Anda...">
    <button onclick="sendMessage()">Kirim</button>

    <script>
        function sendMessage() {
            let userMessage = $("#user-input").val();
            if (userMessage.trim() === "") return;

            // Tampilkan pesan pengguna di chat-box
            $("#chat-box").append("<p class='user'><strong>Anda:</strong> " + userMessage + "</p>");

            // Kirim pesan ke server PHP
            $.ajax({
                url: "chatgpt.php",
                type: "POST",
                data: { message: userMessage },
                success: function(response) {
                    // Tampilkan balasan ChatGPT
                    $("#chat-box").append("<p class='assistant'><strong>ChatGPT:</strong> " + response + "</p>");
                    $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight); // Auto scroll ke bawah
                },
                error: function() {
                    alert("Terjadi kesalahan! Silakan coba lagi.");
                }
            });

            // Kosongkan input
            $("#user-input").val("");
        }
    </script>

</body>
</html>
