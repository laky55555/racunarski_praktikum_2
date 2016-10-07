<?php require_once __SITE_PATH . '/view/_header.php'; ?>

    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-15">
                    <div class="section">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12" style="border: double 1px; background:#00415d; border-collapse: separate;  border-radius: 20px 70px;">
                                    <div class="row">
                                        <div class="col-md-3" style="width: 25%; height: 200px;">
                                            <canvas id="deckCanvas" height="200px" width="300px"></canvas>
                                        </div>
                                        <div class="col-md-3" style="width: 50%; height: 200px; text-align: center;">
                                            <canvas id="topCanvas" height="200px" width="600px"></canvas>
                                        </div>
                                        <div class="col-md-3" style="width: 25%; height: 200px;">
                                            <canvas id="brisCanvas" height="200px" width="250px"></canvas>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3" style="width: 31.25%; height: 350px;">
                                            <canvas id="leftCanvas" height="350px" width="330px"></canvas>
                                        </div>
                                        <div class="col-md-3"
                                             style="width: 37.5%; height: 350px; border: double 1px; background:#53A3CD; border-collapse: separate;  border-radius: 50px;">
                                            <canvas id="thrownCanvas" height="350" width="405"></canvas>
                                        </div>
                                        <div class="col-md-3" style="width: 31.25%; height: 350px;">
                                            <canvas id="rightCanvas" height="350px" width="330px"></canvas>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3" style="width: 25%; height: 200px;">
                                            <canvas id="pointsCanvas" height="200px" width="300px"></canvas>
                                        </div>
                                        <div class="col-md-3"
                                             style="width: 47%; height: 200px; position:relative; text-align: center;">
                                            <div style="width: 100%; position: absolute;  bottom: 0;">
                                                <div id="cards" style="display:inline-block;"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="width: 25%; height: 200px;">
                                            <canvas id="turnCanvas" height="200px" width="300px"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br><br><br><br><br><br><br><br><br><br>
        <div id="finished" class="modal-backdrop in">
            <div class="modal-dialog" style="vertical-align: middle; opacity:0.95 !important;">
                <div class="modal-content" style="text-align: center;">
                    <div class="modal-header">
                        <h4 class="modal-title">IGRA JE GOTOVA!</h4>
                    </div>
                    <div class="modal-body">
                        <table style="margin: 0 auto;">
                            <tr>
                                <td style="text-align: center;">
                                    <p id="msg" style="font-size: 30px;"></p>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <p id="points"></p>
                                </td>
                            </tr>
                            <td style="text-align: center;">
                                <p id="opPoints"></p>
                            </td>
                            </tr>
                            <tr style="display:inline-block;">
                                <td>
                                    <a id="returnBtn" class="btn btn-primary" style="width: 350px;">Vrati se u sobu.</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="view/javascript/draw_functions.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#navbar").append("<li id='surrender' style='background-color: red; cursor: pointer;'>" +
                '<a id="surrenderBtn">Odustani</a>' +
                "</li>");
            var surrBtn = $('#surrenderBtn');
            surrBtn.on('click', function () {
                $.ajax({
                    type: "post",
                    url: "<?php echo $dirname . '/index.php?rt=igra/odustajem'; ?>",
                    dataType: 'json',
                    success: function (data) {
                        $("#surrender").remove();

                    },
                    error: function (status) {
                        console.log("greska u odustajanju: " + status);
                    }
                });
            })
            dealCards();
        });

        var finished = $('#finished');
        finished.hide();
        var playersPosition;
        var roundInfo;
        var refGameIntId;

        function insertPlayerCards() {
            $('#cards').empty();
            var cards = [];
            for (var i = 0; i < roundInfo.cards.length; ++i) {
                if (roundInfo.cards[i] === -1) {
                    continue;
                }
                var btn = '<button style="height: 170px;width:145px; background: url(slike/slika' + roundInfo.cards[i] + '.jpg)"'
                    + '" id="card' + i + '" class="karta"/>';
                cards[i] = $(btn);

                if (roundInfo.isOnTurn() === 1) {
                    cards[i].on('click', {"kliknuta_karta": roundInfo.cards[i]}, function (object) {
                        $.ajax({
                            type: "post",
                            url: "<?php echo $dirname . '/index.php?rt=igra/obradi_bacanje'; ?>",
                            dataType: 'json',
                            data: {"karta": object.data.kliknuta_karta},
                            success: function (data) {
                                roundInfo.turn = 0;
                                roundInfo.throwCard(object.data.kliknuta_karta);
                                playersPosition.addThrownCard(object.data.kliknuta_karta, 'down');
                                insertPlayerCards();
                                refreshGame();
                            },
                            error: function (status) {
                                console.log("greska bacio kartu: " + status);
                                console.log(object.data.kliknuta_karta);
                            }
                        });
                    });
                }
                $('#cards').append(cards[i]);
            }
        }


        function dealCards() {
            $.ajax({
                type: "post",
                url: "<?php echo $dirname . '/index.php?rt=igra/stanje_igre'; ?>",
                dataType: 'json',
                success: function (data) {
                    playersPosition = orderPlayers(data.imena_igraca);
                    roundInfo = new RoundInfo(data);
                    insertPlayerCards();
                    loadPicsAndDraw(data);
                    drawPoints(data.moji_bodovi, data.tudi_bodovi);
                    refreshGame();

                    if (roundInfo.isFinished() && data.bacene_karte.length === 0) {
                        drawWinnerMsg(data.moji_bodovi, data.tudi_bodovi, '<?php echo __SITE_URL . "/index.php?rt=igra/kraj_igre" ?>');
                    }
                },
                error: function (status) {
                    console.log("greska dohvat podataka: " + status.responseText)
                }
            });
        }

        function refreshGame() {
            $.ajax({
                type: "post",
                url: "<?php echo $dirname . '/index.php?rt=igra/stanje_igre'; ?>",
                dataType: 'json',
                success: function (data) {
                    if (data.bacene_karte.length !== roundInfo.numOfDrawnCards) {
                        if (data.bacene_karte.length === 0) {
                            drawThrownCards(data.izasle_u_prosloj, data.imena_igraca, roundInfo.firstPlayed);
                            prepareForNewRound(data);
                            roundInfo.numOfDrawnCards = 0;
                        } else {
                            drawThrownCards(data.bacene_karte, data.imena_igraca, roundInfo.firstPlayed);
                        }
                    }
                    if (data.jesam_na_potezu === 1 && data.bacene_karte.length !== 0) {
                        roundInfo.turn = 1;
                        insertPlayerCards();
                        drawOnTurn("<?php echo $_SESSION['username']; ?>");
                    } else {
                        setTimeout(refreshGame, 2500);
                        var name = data.imena_igraca[(roundInfo.numOfDrawnCards + roundInfo.firstPlayed) % data.imena_igraca.length];
                        drawOnTurn(name);
                    }
                },
                error: function (status) {
                    console.log("greska dohvat podataka: " + status.responseText)
                }
            });
        }

        function prepareForNewRound(data) {
            roundInfo = new RoundInfo(data);
            if (!roundInfo.isFinished()) {
                roundInfo.firstPlayed = findFirstPlayer(data);
                setTimeout(function () {
                    playersPosition.clearRound();
                    drawDeck(data.broj_karti_u_spilu);
                    clearCanvas("thrownCanvas", "#53A3CD");
                    if (data.jesam_na_potezu === 1) {
                        roundInfo.turn = 1;
                    }
                    insertPlayerCards();
                }, 2000);
                drawPoints(data.moji_bodovi, data.tudi_bodovi);
                return;
            } else {
                drawWinnerMsg(data.moji_bodovi, data.tudi_bodovi, '<?php echo __SITE_URL . "/index.php?rt=igra/kraj_igre" ?>');
            }
        }

        function drawThrownCards(bacene_karte, imena_igraca, index) {
            index = (roundInfo.numOfDrawnCards + index) % imena_igraca.length;
            var k = roundInfo.numOfDrawnCards;
            while (roundInfo.numOfDrawnCards < bacene_karte.length) {
                var playerThrown = imena_igraca[index];
                var thrownCard = bacene_karte[k];
                if (playerThrown === "<?php echo $_SESSION['username']; ?>") {
                    roundInfo.throwCard(thrownCard);
                }
                playersPosition.addThrownCard(thrownCard, playersPosition.namePositionMap.get(playerThrown));
                var img = new Image();
                (function (thrownCard, img) {
                    img.onload = function () {
                        drawThrownCard(playersPosition.getPositionThrown(thrownCard), img);
                    };
                })(thrownCard, img);
                img.src = 'slike/slika' + thrownCard + '.jpg';
                roundInfo.numOfDrawnCards++;
                index++;
                k++;
                index %= imena_igraca.length;
                k %= imena_igraca.length;
            }
        }

        function RoundInfo(data) {
            this.cards = data.moje_karte;
            this.finished = data.gotovo;
            for (var i = 0; i < data.bacene_karte.length; ++i) {
                this.throwCard(data.bacene_karte[i]);
            }
            this.turn = data.jesam_na_potezu;
            this.firstPlayed = findFirstPlayer(data);
            this.numOfDrawnCards = 0;
        }

        function findFirstPlayer(data) {
            var firstPlayed = parseInt(data.igrac_na_potezu) - data.bacene_karte.length;
            if (firstPlayed < 0) {
                firstPlayed += data.imena_igraca.length;
            }
            return firstPlayed;
        }

        RoundInfo.prototype.getCards = function () {
            return this.cards;
        };

        RoundInfo.prototype.isFinished = function () {
            if (this.finished == 1) {
                return true;
            }
            return false;
        };

        RoundInfo.prototype.isOnTurn = function () {
            return this.turn;
        };

        RoundInfo.prototype.throwCard = function (card) {
            this.turn = 0;
            var index = this.cards.indexOf(card);
            if (index > -1) {
                this.cards.splice(index, 1);
                this.thrown = card;
            }
        };

        //-------------------------------------------------------------------------------------------------------------

        function orderPlayers(players) {
            var r, t, l, i;
            if (players.length == 2) {
                i = players.indexOf('<?php echo $_SESSION['username']; ?>');
                i = (++i) % players.length;
                t = players[i];
                return new PlayersArrangement('', t, '', '<?php echo $_SESSION['username']; ?>');
            }
            var counter = 0;
            var bool = false;
            i = players.indexOf('<?php echo $_SESSION['username']; ?>');
            i = (++i) % players.length;
            r = players[i];
            i = (++i) % players.length;
            t = players[i];
            i = (++i) % players.length;
            l = players[i];
            return new PlayersArrangement(r, t, l, '<?php echo $_SESSION['username']; ?>');
        }


        function PlayersArrangement(right, top, left, down) {
            this.positionNameMap = new Map();
            this.positionNameMap.set('right', right);
            this.positionNameMap.set('top', top);
            this.positionNameMap.set('left', left);
            this.positionNameMap.set('down', down);
            this.namePositionMap = new Map();
            this.namePositionMap.set(right, 'right');
            this.namePositionMap.set(top, 'top');
            this.namePositionMap.set(left, 'left');
            this.namePositionMap.set(down, 'down');
            this.positionCardMap = new Map();
        }

        // Add methods like this.  All Person objects will be able to invoke this
        PlayersArrangement.prototype.getLeft = function () {
            return this.positionNameMap.get('left');
        };
        PlayersArrangement.prototype.getRight = function () {
            return this.positionNameMap.get('right');
        };
        PlayersArrangement.prototype.getTop = function () {
            return this.positionNameMap.get('top');
        };
        PlayersArrangement.prototype.getThrownCard = function (player) {
            return this.positionCardMap.get(this.namePositionMap.get(player));
        };
        PlayersArrangement.prototype.getPositionThrown = function (card) {
            var mapIter = this.positionCardMap.entries();
            var obj = mapIter.next().value;
            while (obj !== undefined) {
                if (obj[1] === card) {
                    return obj[0];
                }
                obj = mapIter.next().value;
            }
            console.log("Nije pronasao igraca za bacenu kartu...");
            return undefined;
        }
        PlayersArrangement.prototype.addThrownCard = function (card, position) {
            this.positionCardMap.set(position, card);
        };
        PlayersArrangement.prototype.clearRound = function () {
            this.positionCardMap = new Map();
        };

    </script>
<?php require_once __SITE_PATH . '/view/_footer.php'; ?>
