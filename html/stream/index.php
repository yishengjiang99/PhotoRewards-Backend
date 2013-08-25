<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
         
        <script>
        var source = 'THE SOURCE';
         
        function start_task()
        {
            source = new EventSource('t2.php');
             
            //a message is received
            source.addEventListener('message' , function(e)
            {
                var result = JSON.parse( e.data );
                 
                add_log(result.message);
                 
                document.getElementById('progressor').style.width = result.progress + "%";
                 
                if(e.data.search('TERMINATE') != -1)
                {
                    add_log('Received TERMINATE closing');
                    source.close();
                }
            });
             
            source.addEventListener('error' , function(e)
            {
                add_log('Error occured');
                 
                //kill the object ?
                source.close();
            });
        }
         
        function stop_task()
        {
            source.close();
            add_log('Interrupted');
        }
         
        function add_log(message)
        {
            var r = document.getElementById('results');
            r.innerHTML += message + '<br>';
            r.scrollTop = r.scrollHeight;
        }
        </script>
    </head>
    <body>
        This is NOT AJAX
        <br />
        <input type="button" onclick="start_task();"  value="Start Long Task" />
        <input type="button" onclick="stop_task();"  value="Stop Task" />
        <br />
        <br />
         
        Results
        <br />
        <div id="results" style="border:1px solid #000; padding:10px; width:300px; height:200px; overflow:auto; background:#eee;"></div>
        <br />
         
        <div style="border:1px solid #ccc; width:300px; height:20px; overflow:auto; background:#eee;">
            <div id="progressor" style="background:#07c; width:0%; height:100%;"></div>
        </div>
         
    </body>
</html> 
