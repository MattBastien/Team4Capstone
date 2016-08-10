    //Opens and closes the video pane
    function togglePane(input) {
      if (document.getElementById('security').innerHTML.search('img width') == -1) {
        //Spawn a new div
        var div = document.createElement('div');
        div.className = 'row';
        div.id = 'videofeed';
        div.innerHTML ='<br><img width="1328" style="-webkit-user-select: none; position: relative; top: 200px; bottom: 5000px" src="http://192.168.43.1:8080/video">'
        document.getElementById('security').appendChild(div);
      } else {
        //Remove the div
        document.getElementById('videofeed').remove();
      }
    }
    //Poll push button states and feed to server
    function submitFcn() {
      elements = document.querySelectorAll('[id^="device"]');
      desc = [];
      value = [];
      var redir = "?";
      for (i = 0; i < elements.length; ++i) { 
        id = elements[i].id; 
        value[i] = isOn(id);
        desc[i] = elements[i].getAttribute('class').split(' toggle-light')[0];
        redir += desc[i].concat('=').concat(value[i]).concat('&');
      }
      window.location.replace(redir);
    }
    function isOn(button) {
      $search = document.getElementById(button).innerHTML.search('toggle-on active');
      if ($search != -1) { $result = 1; } else { $result = 0; }
      return $result
    } 
