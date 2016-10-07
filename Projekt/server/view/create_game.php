<?php require_once __SITE_PATH . '/view/_header.php'; ?>


<div class="container">
    <div class="row">
        <div class="col-md-6" id="onlinePlayers">
            <div class="col-md-12">
                <div class="container">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="page-header">
                                <h1>Online igrači</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5" style="border:1px solid #53A3CD; height: 500px; overflow-y: scroll;">
                            <table id="onlineUsers" class="table">
                                <thead>
                                <tr>
                                    <th><span style="color: #00415d">Nick</span></th>
                                    <th style="text-align: center;"><span
                                            style="color: #00415d">Postotak uspješnosti</span></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="page-header" id="onlineGameRoom">
                <h1 style="margin-bottom: 18px;">Igrači u sobi</h1>
                <br>
                <ul id="playingUsers" class="list-group">
                    <li class="list-group-item list-group-item-info">Naziv igrača</li>
                </ul>
                <a href="#" class="btn btn-primary" id="cancelGameButton"
                   style="margin-top: 50px; width: 75%; margin-left: 12.5%;">Odustani!</a>
            </div>
            <a href="#" class="btn btn-block btn-primary" style="margin-top: 30%;" id="createGameButton2">Napravi igru
                za 2!</a>
            <a href="#" class="btn btn-block btn-primary" style="margin-top: 5%;" id="createGameButton4">Napravi igru za
                4!</a>
            <a href="#" class="btn btn-block"
               style="margin-top: 5px; background-color: #1CB94E; width: 75%; margin-left: 12.5%;"
               id="startGameButton">Pokreni igru!</a>
            <br><br>
        </div>
    </div>
    <div class="row" style="text-align: center;">
        <div id="acceptGamePopup" class="modal-backdrop in">
            <div class="modal-dialog" style="vertical-align: middle; opacity:0.95 !important;">
                <div class="modal-content" style="text-align: center;">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true" onclick="answerOnInvite(false)">×
                        </button>
                        <h4 class="modal-title">POZIV ZA IGRU</h4>
                    </div>
                    <div class="modal-body">
                        <table>
                            <tr>
                                <td style="text-align: center;">
                                    <p id="inviteMsg"></p>
                                </td>
                            </tr>
                            <tr style="display:inline-block;">
                                <td>
                                    <a class="btn btn-default" style="width: 273px;"
                                       onclick="answerOnInvite(false)">NE</a>
                                </td>
                                <td>
                                    <a class="btn btn-primary" style="width: 273px;"
                                       onclick="answerOnInvite(true)">DA</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="waitAnswerPopup" class="modal-backdrop in">
            <div class="modal-dialog" style="vertical-align: middle;">
                <div class="modal-content" style="text-align: center; height: 250px">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true" onclick="cancelInvite()">×</button>
                        <h4 class="modal-title">CEKAM ODGOVOR...</h4>
                    </div>
                    <div class="modal-body">
                        <table>
                            <tr>
                                <td style="text-align: center;">
                                    <p id="waitMsg"></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <br/><br/>
                                    <div id="loading"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // treba jos popraviti width linkova od acceptGamePopup

    var refTableId;// = setInterval(refreshTable, 4000);
    var checkIfInvId; // = setInterval(checkIfInvited, 4000);
    var refGameId;
    var responseCntdwnId;
    var createdRoom;

    var acceptGamePopup = $("#acceptGamePopup");
    var waitInvitePopup = $("#waitAnswerPopup");
    var createGameBtn2 = $('#createGameButton2');
    var createGameBtn4 = $('#createGameButton4');
    var startGameBtn = $('#startGameButton');
    var cancelGameBtn = $('#cancelGameButton');
    var onlinePlayersDiv = $("#onlinePlayers");
    var gameRoom = $("#onlineGameRoom");

    var hiddenGameClass = "playerInviteHidden";
    // izbrisati
    var data;
    var gameRoomPlayers;

    refreshTable();
    cancelGameBtn.on("click", cancelGame);
    startGameBtn.on("click", startGame)
    createGameBtn2.on("click", {broj_igraca: "2"}, createRoom);
    createGameBtn4.on("click", {broj_igraca: "4"}, createRoom);

    startGameBtn.hide();
    waitInvitePopup.hide();
    acceptGamePopup.hide();

    refTableId = setInterval(refreshTable, 2000);
    if (<?php echo $postoji_soba; ?>) {
        inRoom();
    }
    else {
        outRoom();
    }

    /**

     */
    function inRoom() {
        createGameBtn2.hide();
        createGameBtn4.hide();
        gameRoom.show();
        $('.playerInviteHidden').each(function () {
            this.className = "btn btn-primary invitePlayerBtn playerInviteShown"
        });
        hiddenGameClass = "playerInviteShown";
        gameRoomPlayers = [];
        refGameId = setInterval(refreshGame, 1000);
        clearInterval(checkIfInvited);
        //refTableId = setInterval(refreshTable, 4000);

    }

    function outRoom() {
        checkIfInvId = setInterval(checkIfInvited, 4000);
        //refTableId = setInterval(refreshTable, 4000);
        clearInterval(refGameId);

        gameRoom.hide();
        createGameBtn2.show();
        createGameBtn4.show();
        onlinePlayersDiv.show();
        startGameBtn.hide();

        waitInvitePopup.hide();
        acceptGamePopup.hide();

        $("li.invited").remove();
        $('.playerInviteShown').each(function () {
            this.className = "btn btn-primary invitePlayerBtn playerInviteHidden"
        });
        hiddenGameClass = "playerInviteHidden";

        gameRoomPlayers = null;
    }

    /**
     *
     */
    function createRoom(object) {
        createdRoom = true;
        broj_igraca = object.data.broj_igraca;
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/stvori_sobu'; ?>",
            dataType: 'json',
            data: {"broj_igraca": broj_igraca},
            success: function (data) {
                inRoom();
            },
            error: function (status) {
                console.log("dogodila se greska sa statusom: " + status.responseText)
            }
        });
        console.log("nakon ajaxa");
    }

    function cancelGame() {
        createdRoom = false;
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/izbaci_iz_sobe'; ?>",
            dataType: 'json',
            success: function (data) {
                console.log("uspio cancel Game");
                outRoom();
            },
            error: function (status) {
                console.log("ajax greska: cancelGame" + status.responseText);
            }
        });
    }


    function startGame() {
        window.location = "<?php echo $dirname . '/index.php?rt=online/konstruiraj_igru'; ?>";
        /*
         $.ajax({
         type: "post",
         url: "<?php echo $dirname . '/index.php?rt=online/konstruiraj_igru'; ?>",
         dataType: 'json',
         //data: data,
         success: function (data) {
         console.log("uspio napravit igru");
         },
         error: function(status){
         console.log("ajax greska: startGame" + status);
         }
         });*/
    }

    function refreshTable() {
        //console.log("u refreshTable");
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/svi_online_users'; ?>",
            dataType: 'json',
            //data: data,
            success: function (data) {
                //data = [{username: "Laki", id: 1}, {username: "Mia", id: 2}, {username: "Mira", id: 3}];
                $('#onlineUsers tbody  > tr').remove();
                for (var i = 0; i < data.length; i++) {
                    if (data[i].username === '<?php echo $_SESSION['username']; ?>') {
                        continue;
                    }
                    var usrName = data[i].username;
                    var usrId = data[i].id;
                    var winsPlayed = 'Nan';
                    if (parseInt(data[i].broj_odigralih) !== 0) {
                        winsPlayed = parseInt(data[i].broj_pobjeda) / parseInt(data[i].broj_odigralih) * 100;
                    }
                    $('#onlineUsers tbody').append(
                        '<tr><td><p class="text-primary" style="font-size:18px;">' + usrName + '</p></td>' +
                        '<td style="text-align: center;"><p class="text-primary" style="font-size:18px;">' + parseInt(winsPlayed) + '%</p></td>' +
                        '<td><button id="' + i + 'call" class="btn btn-primary invitePlayerBtn ' + hiddenGameClass + '">Pozovi</button></td></tr>'
                    );
                    $('#' + i + 'call').on("click", (function (name) {
                        return function () {
                            invitePlayer(name);
                        };
                    })(usrId));
                }
            }
        });
    }

    // treba mijenjati koliko je igraca u sobi
    function refreshGame() {
        //console.log("refreshGame se pozvao");
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/cekaj_pocetak_igre'; ?>",
            dataType: 'json',
            success: function (data) {

                console.log("poziv refresGame uspio");
                if (data.id_igre != -1)
                    window.location = "<?php echo $dirname . '/index.php?rt=igra'; ?>";
                var brojAktivnih = 0;
                gameRoomPlayers = [];
                for (var i = 0; i < data.igraci.length; ++i) {
                    gameRoomPlayers.push(data.igraci[i]);
                    //console.log(data.igraci[i]);
                    if (data.igraci[i] !== "")
                        brojAktivnih++;
                }
                //console.log("ajax success: refreshGame");
                if (brojAktivnih < parseInt(data.broj_igraca)) {
                    startGameBtn.hide();
                    $('.playerInviteHidden').each(function () {
                        this.className = "btn btn-primary invitePlayerBtn playerInviteShown"
                    });
                } else {
                    startGameBtn.show();
                    $('.playerInviteShown').each(function () {
                        this.className = "btn btn-primary invitePlayerBtn playerInviteHidden"
                    });
                }
                //moze se i bolje...
                $("li.invited").remove();
                for (var i = 0; i < gameRoomPlayers.length; i++) {
                    $("#playingUsers").append("<li class='list-group-item invited invPlayersStyle'>" + gameRoomPlayers[i] + "</li>");
                }
            },
            error: function (status) {
                console.log("refreshGame error: " + status.responseText);
            }
        });
    }

    // server ceka max 15-ak sekundi da odgovori igrac i onda salje da ne
    // stavio da ne mozes pozvati 100 ljudi pa tko dode...
    function invitePlayer(playerNick) {
        $('#waitMsg').text("Cekam odgovor " + playerNick);
        waitInvitePopup.show();
        console.log("sada smo nekoga pozvali daa igra s nama" + playerNick);
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/pozovi_osobu_u_sobu' ?>",
            dataType: "json",
            data: {id_igraca: playerNick},
            success: function (data) {
                //console.log("ajax success: invitePlayer");
                refreshTable();
                if (gameRoomPlayers.length === 3) {
                    $('.playerInviteShown').each(function () {
                        this.className = "btn btn-primary invitePlayerBtn playerInviteHidden"
                    });
                    hiddenGameClass = "playerInviteHidden";
                    startGameBtn.show();
                }
                waitInvitePopup.hide();
            },
            error: function (status) {
                console.log("ajax error: invitePlayer");
            }
        });

    }

    // 7 sekundi za odogovor ako je igrac pozvan
    function checkIfInvited() {
        if (createdRoom) {
            return;
        }
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/obradi_poziv_u_sobu' ?>",
            dataType: "json",
            data: null,
            success: function (data) {
                //data[0].invited
                console.log("ajax: success " + data.poziv);
                if (data.poziv !== 'nema_poziva') {
                    console.log('u ifu sam');
                    var allPlayers = "";
                    var del = '';
                    for (var i = 0; i < data.igraci.length; ++i) {
                        if (data.igraci[i] === '') {
                            break;
                        }
                        allPlayers += del + data.igraci[i];
                        del = ',';
                    }
                    showAcceptGameDiv("Želite li igrati s " + allPlayers +
                        " u igri s " + data.broj_igraca + " igraca?");
                    /*responseCntdwnId = setTimeout(function () {
                     countdownResponseTime();
                     }, 7000);*/
                }
            },
            error: function (status) {
                console.log("ajax error: checkIfInvited" + status);
            }
        });
    }

    /*function countdownResponseTime() {
     acceptGamePopup.hide();
     checkIfInvId = setInterval(checkIfInvited, 2000);
     }*/


    function answerOnInvite(response) {
        if (createdRoom) {
            return;
        }
        console.log("answerOnInvite " + response);
        acceptGamePopup.hide();
        if (!response) {
            checkIfInvId = setInterval(checkIfInvited, 4000);
        }
        //clearTimeout(responseCntdwnId);
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/odgovori_na_poziv' ?>",
            dataType: "json",
            data: {odgovor: response},
            success: function (data) {
                console.log("ajax success: answerOnInvite")
                if (data.ubacen_u_sobu === 'true')
                    inRoom();
            },
            error: function (status) {
                console.log("ajax error: answerOnInvite" + status.responseText);
            }
        });
    }

    function openAcceptedGame() {
        $.ajax({
            type: "post",
            url: "<?php echo $dirname . '/index.php?rt=online/online_ajax'; ?>",
            dataType: 'json',
            success: function (data) {
                clearInterval(checkIfInvId);
                clearInterval(refTableId);
                onlinePlayersDiv.hide();
                createGameBtn2.hide();
                createGameBtn4.hide();
                gameRoom.show();
            }
        });
    }

    function showAcceptGameDiv(inviteMsg) {
        clearInterval(checkIfInvId);
        $('#inviteMsg').text(inviteMsg);
        acceptGamePopup.show();
    }
</script>


<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
