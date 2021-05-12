function showModal(heading, body){
    var btn = document.getElementById("btn-staticBackdrop");
    var lbl = document.getElementById("staticBackdropLabel");
    var bdy = document.getElementById("staticBackdropBody");

    lbl.innerHTML = heading;
    bdy.innerHTML = body;

    btn.click();
}

function callEndpoint(url, body, username, password, onSuccess, onError){
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (xhttp.status === 200) {
                onSuccess(this);
            } else {
                onError(this);
            }
       }
    };

    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/json");
    xhttp.setRequestHeader("Authorization", "Basic " + btoa(username+":"+password));
    xhttp.send(JSON.stringify(body));
}
