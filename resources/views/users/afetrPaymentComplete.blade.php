<section class="modal-section">
	<div class="container">
		<div class="modal-box-inner">
			<div class="modal-box-icon">
				<img src="https://www.nimbleappgenie.live/dba-interest/public/images/check-circle-regular.svg" alt="image">
			</div>
			<div class="modal-box-content">
				<h3>Sucessfull</h3>
				<p>{{ date("F, d Y, H:i A")}}</p>
			</div>
		</div>
	</div>
</section>

<style>
	body{margin: 0; padding: 0;}
	.modal-section{display: flex; align-items: center; justify-content: center; height: 100vh; background: rgb(0 0 0 / 25%);}
	.modal-box-inner {width: 500px; height: 500px; overflow: hidden; border-radius: 30px; box-shadow: 0 0 10px 10px rgb(0 0 0 / 12%);}
	.modal-box-icon{background: linear-gradient(to right, #95E6BB, #1FCC70); height: 50%; display: flex; align-items: center; justify-content: center;}
	.modal-box-content{text-align: center; padding: 20px; background: #fff; height: 50%;}
	.modal-box-content h3 { font-size: 30px; font-weight: bold; font-family: sans-serif; margin: 0 0 20px;}
	.modal-box-content p{font-size: 20px; font-weight: 400; color: #000; font-family: sans-serif; margin: 0;}
	.modal-box-icon img {max-width: 90px;}
</style>


