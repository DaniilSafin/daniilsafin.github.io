$(function() {
  $('.login_button').click(function() {
   doLogin();
  });
  
  $('#send_chat_form').submit(function() {
    var message = $(this).find('.message').val();
    sendMsg(message);
    $('#chat').append('<div class="msg">me: ' + message + '</div>');
    $(this).find(".message").val('');
    return false;
  });
});

function handleMessage(aJSJaCPacket) {
  $('#chat').append('<div class="msg">' + aJSJaCPacket.getFromJID() + ': ' + aJSJaCPacket.getBody().htmlEnc() + '</div>');
}

function handlePresence(aJSJaCPacket) {
  var html = '<div class="msg">';
  if (!aJSJaCPacket.getType() && !aJSJaCPacket.getShow()) {
    html += '<b>'+ aJSJaCPacket.getFromJID() +' has become available.</b>';
  } else {
    html += '<b>'+ aJSJaCPacket.getFromJID() +' has set his presence to ';
    if (aJSJaCPacket.getType()) {
      html += aJSJaCPacket.getType() + '.</b>';
    } else {
      html += aJSJaCPacket.getShow() + '.</b>';
    }
    if (aJSJaCPacket.getStatus()) {
      html += ' ('+aJSJaCPacket.getStatus().htmlEnc()+')';
    }
  }
  html += '</div>';

  $('#chat').append(html);
}

function handleError(e) {
  $('#log').append("An error occured:<br />"+ ("Code: "+ e.getAttribute('code')+"\nType: " + e.getAttribute('type')+ "\nCondition: "+ e.firstChild.nodeName).htmlEnc());
  if (con.connected()) {
    con.disconnect();
  }
}

function handleConnected() {
  $('#login').hide();
  $('#log').html('');
  $('#send_chat').show();
  
  var packet = new JSJaCPresence();
  packet.setTo("#chatroom@conference.example.com/whoahbot");
  packet.appendNode('x', {xmlns: "http://jabber.org/protocol/muc"});
  con.send(packet);
}

function handleDisconnected() {
  $('#chat').hide();
  $('#login').show();
}

function handleIqVersion(iq) {
  con.send(iq.reply([
                     iq.buildNode('name', 'jsjac simpleclient'),
                     iq.buildNode('version', JSJaC.Version),
                     iq.buildNode('os', navigator.userAgent)
                     ]));
  return true;
}

function handleIqTime(iq) {
  var now = new Date();
  con.send(iq.reply([iq.buildNode('display', now.toLocaleString()),
                     iq.buildNode('utc', now.jabberDate()),
                     iq.buildNode('tz', now.toLocaleString().substring(now.toLocaleString().lastIndexOf(' ')+1))
                     ]
          ));
  return true;
}

function doLogin(form) {
  $('#log').html('');

  try {
    // setup args for contructor
    oArgs = new Object();
    oArgs.httpbase = 'http://whoahbot.example.com/http-bind/';
    oArgs.timerval = 2000;

    if (typeof(oDbg) != 'undefined') {
      oArgs.oDbg = oDbg;
    }
    con = new JSJaCHttpBindingConnection(oArgs);

    setupCon(con);

    // setup args for connect method
    oArgs = new Object();
    oArgs.domain = 'example.com';
    oArgs.username = $('#username').val();
    oArgs.resource = 'jsjac_simpleclient';
    oArgs.pass = $('#password').val();
    oArgs.register = false;
    con.connect(oArgs);
  } catch (e) {
    $('#log').append(e.toString());
  } finally {
    return false;
  }
}

function setupCon(con) {
  con.registerHandler('message',handleMessage);
  con.registerHandler('presence',handlePresence);
  con.registerHandler('onconnect',handleConnected);
  con.registerHandler('onerror',handleError);
}

function sendMsg(message) {
  try {
    var aMsg = new JSJaCMessage();
    aMsg.setTo(new JSJaCJID("#chatroom@conference.example.com/whoahbot"));
    aMsg.setType('groupchat');
    aMsg.setBody(message);
    con.send(aMsg);
  } catch (e) {
    $('#chat').html("<div class='msg error''>Error: "+ e.message +"</div>");
  }
}

function quit() {
  var p = new JSJaCPresence();
  p.setType("unavailable");
  con.send(p);
  con.disconnect();

  $('.login').show();
}

onerror = function(e) {
  $('#log').append(e);
  $('#door').show();

  if (con && con.connected()) {
    con.disconnect();
  }
  return false; 
};

onunload = function() {
  if (typeof con != 'undefined' && con && con.connected()) {
    if (con._hold) {// must be binding
      (new JSJaCCookie('btype','binding')).write();
    }
    
    if (con.suspend) {
      con.suspend(); 
    }
  }
};
