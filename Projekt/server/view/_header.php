<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8">
    <title>Briskula</title>
    <link rel="stylesheet" href="<?php echo __SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<?php
if ((!isset($greska) || $greska === '') && isset($_SESSION['username'])) {
    ?>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <div class="navbar-brand"><span>Dobrodošao <?php echo $_SESSION['username']; ?>!</span></div>
            </div>
            <div class="collapse navbar-collapse" id="navbar-ex-collapse">
                <ul id="navbar" class="nav navbar-nav navbar-right">
                    <li class="active">
                        <a href="<?php echo __SITE_URL . '/index.php?rt=online'; ?>">Igra</a>
                    </li>
                    <li>
                        <a id="logout" href="<?php echo __SITE_URL . '/index.php?rt=prijava/logout'; ?>">Odjavi se</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}
?>

<?php
$arr = explode('?', $_SERVER['REQUEST_URI']);
$dirname = dirname($arr[0]);
?>


<script type="text/javascript">
    var logout = $("#logout");

    logout.on("click", function () {
        $(this).className = "active";
    });
</script>
