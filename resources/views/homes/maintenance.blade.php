<!DOCTYPE html>
<html>
<head>

	 <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

<style>
	*{
		padding: 0px;
		margin:0px;
		box-sizing: border-box;
	}

	.dafri_page h1 {
    font-size: 52px;
    font-weight: bold;
    color: #000;
    text-align: center;
    padding: 10px;
    margin-top: 0px;
}

.dafri_page p {
    text-align: center;
    color: black;
    font-size: 18px;
}

.dafri_page {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

}

img.dafri_img {
    width: 280px;
}

</style>
</head>
<body>

<section class="maintenance_page" style="width: 600px;max-width: 100%;padding:10px;margin:0px auto; height: 100vh;display: flex;align-items: center;">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<div class="dafri_page">
				<img src="{{HTTP_PATH}}/public/img/2639855_maintenance_icon.svg" style="margin-top: 0px;padding: 10px;text-align: center;" class="dafri_img">
				<h1>Maintenance Mode</h1>
				<p>This site is currently under going scheduled maintenance.
                    <br> Please check back soon.</p>
			</div>
			</div>
		</div>
	</div>
</section>

</body>
</html>