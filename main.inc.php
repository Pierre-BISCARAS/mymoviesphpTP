<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>MyMoviesPHP</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="./css/styles.css">
  <script src="./scripts/scripts.js"></script>
</head>
<body>
    <div class="menutop w3-top">
        <?php 
        include './inc/top.php';
        ?>
    </div>
    <div class="menuleft">
        <?php 
        include './inc/left.php';
        ?>
    </div>
    <div class="maincontent  w3-display-container w3-center">
        <?php 
        include './inc/content.php';
        ?>
    </div>
    <div class="footer">
        <?php 
        include './inc/footer.php';
        ?>
    </div>
</body>
</html>