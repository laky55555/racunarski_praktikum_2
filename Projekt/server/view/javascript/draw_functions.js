


function drawOtherCards(data) {
    var oppositeR = new Image();
    oppositeR.onload = function () {
        for (var i = 0; i < 3; ++i) {
            if (data.imena_igraca.length > 2) {
                drawLeftCards(i, playersPosition.getLeft());
                drawRightCards(i, playersPosition.getRight());
            }
        }
    };
    oppositeR.src = 'slike/rotOpposite.jpg';

    var opposite = new Image();
    opposite.onload = function () {
        for (var i = 0; i < 3; ++i) {
            drawTopCards(i, playersPosition.getTop());
        }
    };
    opposite.src = 'slike/opposite.jpg';
}

function drawTopCards(offset, ime) {
    var c = document.getElementById("topCanvas");
    var ctx = c.getContext("2d");
    var img = new Image();
    img.src = 'slike/opposite.jpg';
    ctx.drawImage(img, 135 + 90 * offset, 5, 110, 187.5);
    ctx.font = "25px Arial";
    ctx.textAlign = "center";
    ctx.fillText(ime, 90, 180);
}

function drawLeftCards(offset, ime) {
    var c = document.getElementById('leftCanvas');
    var ctx = c.getContext("2d");
    var img = new Image();
    img.src = 'slike/rotOpposite.jpg';
    ctx.drawImage(img, 5, 30 + 90 * offset, 207.5, 100);
    ctx.save();
    if (offset === 0) {
        ctx.rotate(Math.PI / 2);
        ctx.font = "25px Arial";
        ctx.textAlign = "center";
        ctx.fillText(ime, 170, -230);
        ctx.restore();
    }
}

function drawRightCards(offset, ime) {
    var c = document.getElementById('rightCanvas');
    var ctx = c.getContext("2d");
    var img = new Image();
    img.src = 'slike/rotOpposite.jpg';
    ctx.drawImage(img, 130, 30 + 90 * offset, 207.5, 100);
    ctx.save();
    if (offset === 0) {
        ctx.rotate(-Math.PI / 2);
        ctx.font = "25px Arial";
        ctx.textAlign = "center";
        ctx.fillText(ime, -170, 100);
        ctx.restore();
    }
}

function drawBriskula(briskula) {
    var c = document.getElementById('brisCanvas');
    var ctx = c.getContext("2d");
    var bris = new Image();
    bris.onload = function () {
        ctx.drawImage(bris, 100, 5, 110, 187.5);
        ctx.rotate(-Math.PI / 2);
        ctx.font = "18px Arial";
        ctx.textAlign = "center";
        ctx.fillStyle = "#53A3CD";
        ctx.fillText("Briškula", -100, 60);
        ctx.restore();
    };
    bris.src = 'slike/slika' + briskula + '.jpg';
}

function drawDeck(numOfCardsInDeck) {
    var c = document.getElementById('deckCanvas');
    var ctx = c.getContext("2d");
    clearCanvas("deckCanvas", "#00415d");
    var pic = new Image();
    pic.onload = function () {
        var i;
        ctx.textAlign = "center";
        for (i = 0; i < numOfCardsInDeck; ++i) {
            ctx.drawImage(pic, 55, 5 + i, 187.5, 110);
        }
        ctx.font = "18px Arial";
        ctx.fillStyle = "#53A3CD";

        ctx.fillText("Ostalo karata:  " + numOfCardsInDeck, 150, 185);
    };
    pic.src = 'slike/rotOpposite.jpg';
}

function drawPoints(myPoints, oppPoints) {
    var c = document.getElementById('pointsCanvas');
    var ctx = c.getContext("2d");
    clearCanvas('pointsCanvas', "#53A3CD");
    ctx.font = "18px Arial";
    ctx.fillStyle = "#53A3CD";
    ctx.textAlign = "center";
    ctx.fillText("Moji bodovi:  " + myPoints, 150, 65);
    ctx.fillText("Protivnički bodovi:  " + oppPoints, 150, 105);
}

function drawThrownCard(position, pic) {
    var c = document.getElementById('thrownCanvas');
    var ctx = c.getContext("2d");
    var x, y;
    switch (position) {
        case 'down':
            x = 170;
            y = 130;
            break;
        case 'left':
            x = 90;
            y = 80;
            break;
        case 'right':
            x = 220;
            y = 80;
            break;
        case 'top':
            x = 170;
            y = 30;
            break;
        default:
            console.log("Error, wrong thrown card position!")
    }
    ctx.drawImage(pic, x, y, 110, 187.5);
    ctx.stroke();
}

function drawOnTurn(ime) {
    var c = document.getElementById('turnCanvas');
    var ctx = c.getContext("2d");
    clearCanvas("turnCanvas", "#00415d");
    ctx.font = "33px Arial";
    ctx.fillStyle = "#53A3CD";
    ctx.textAlign = "center";
    ctx.fillText("Na potezu: ", 150, 65);
    ctx.fillText(ime, 150, 105);
}

function drawWinnerMsg(moji_bodovi, tudi_bodovi, backHref){
    var msg = $('#msg');
    var str;
    var back = $('<a id="create_game_button" class="active btn btn-lg playButtonSize">Natrag u sobu...</a>');
    if(moji_bodovi > tudi_bodovi){
        str = "Čestitamo, pobijedili ste!";
        msg.css("color", "green");
    } else {
        str = "Žao nam je, izgubili ste...";
        msg.css("color", "darkred");
    }
    $('#points').text("Vaši bodovi:  " + moji_bodovi);
    $('#opPoints').text("Protivnički bodovi:  " + tudi_bodovi);
    $('#returnBtn').attr('href',backHref);
    msg.text(str);
    finished.show();
}

function clearEnd() {
    clearCanvas("topCanvas", "#00415d");
    clearCanvas("leftCanvas", "#00415d");
    clearCanvas("rightCanvas", "#00415d");
}

function clearCanvas(selector, color){
    var c = document.getElementById(selector);
    var ctx = c.getContext("2d");
    ctx.fillStyle= color;
    ctx.clearRect(0, 0, c.width, c.height);
}

function scaleIt(source) {
    var can2 = document.createElement('canvas');
    var h = 275, w = 140;
    can2.height = source.height;
    can2.width = source.width;
    var ctx2 = can2.getContext('2d');
    ctx2.drawImage(source, 0, 0, w / 2, h / 2);
    ctx2.drawImage(can2, 0, 0, w / 2, h / 2, 0, 0, w / 4, h / 4);
    return can2;
}

function loadPicsAndDraw(data) {
    drawBriskula(data.briskula);
    drawOtherCards(data);
    drawDeck(data.broj_karti_u_spilu);

}