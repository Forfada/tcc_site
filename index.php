<?php
	include 'config.php';
	include(DBAPI);
	include(HEADER_TEMPLATE);
	if (!isset($_SESSION)) session_start();
?>

<?php if (!empty($_SESSION['message'])) : ?>
	<div class="container d-flex justify-content-center" style="margin-top: 120px;">
		<div class="alert alert-<?php echo $_SESSION['type']; ?> alert-dismissible fade show w-75 text-center" role="alert">
			<?php echo $_SESSION['message']; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
		<?php clear_messages(); ?>
	</div>
<?php endif; ?>

<section id="home" class="section-parallax section-cor3-mode">
  <div class="parallax-wave"> 
    <svg viewBox="0 0 1440 180" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path d="M0,80 C320,160 1120,0 1440,80 L1440,180 L0,180 Z" fill="#f6dfc1" opacity="0.74"/>
      <path d="M0,100 C400,200 1040,40 1440,100 L1440,180 L0,180 Z" fill="#541f33" opacity="0.92"/>
      <path d="M0,130 C500,210 940,90 1440,130 L1440,180 L0,180 Z" fill="#541f33" opacity="0.96"/>
      <path d="M0,150 C600,190 900,110 1440,150 L1440,180 L0,180 Z" fill="#541f33" opacity="0.95"/>
    </svg>
  </div>

  <div class="overlayy">
    <h1>Autocuidado em todas as fases</h1>
    <h2>Confiança, bem-estar e valorização</h2>
    <p>
      Na Lunaris, cada momento é uma oportunidade de se sentir bem.  
      Oferecemos estética avançada com olhar humano, realçando sua beleza natural e promovendo seu bem-estar.
    </p>
  </div>
</section>

<section id="home" class="section-dark section-cor4">
	<div class="swiper">
		<h2 class="txt1 mt-5">Conheça <i>nossos serviços</i></h2>
		<p class="txt4"> Conheça nossos procedimentos de Embelezamento e Autocuidado.</p>
	
		<div class="slide-container">
			<div class="slide-content">
				<div class="card-wrapper swiper-wrapper">
					<!-- Todos os cards do seu código original, sem alterações -->
					<div class="card swiper-slide">
						<div class="image-content"><span class="overlay"></span>
							<div class="card-image">
								<img src="<?php echo BASEURL; ?>procedimentos/imagens/hid_C.jpeg" alt="" class="card-img">
							</div>
						</div>
						<div class="card-content">
							<h2 class="name">HidraColor</h2>
							<p class="description">Realce e destaque a aparência de seus lábios com HidraColor, hidratando, renovando e realçando a cor de seus lábios.</p>
							<button class="buttonc">Saber mais</button>
						</div>
					</div>
					<div class="card swiper-slide">
						<div class="image-content"><span class="overlay"></span>
							<div class="card-image">
								<img src="<?php echo BASEURL; ?>procedimentos/imagens/hid_L.jpeg" alt="" class="card-img">
							</div>
						</div>
						<div class="card-content">
							<h2 class="name">HidraLips</h2>
							<p class="description">Renove e realce a aparência de seus lábios com nossa técnica de HidraLips, hidratando e dando brilho aos seus lábios.</p>
							<button class="buttonc">Saber mais</button>
						</div>
					</div>
					<div class="card swiper-slide">
						<div class="image-content"><span class="overlay"></span>
							<div class="card-image">
								<img src="<?php echo BASEURL; ?>procedimentos/imagens/mic_l.jpeg" alt="" class="card-img">
							</div>
						</div>
						<div class="card-content">
							<h2 class="name">Micropigmentação Labial</h2>
							<p class="description">Defina o contorno de sua boca, realce sua cor natural e corrija assimetrias indesejadas.</p>
							<button class="buttonc">Saber mais</button>
						</div>
					</div>
					<!-- Continuam todos os outros cards exatamente como no seu código original até o final -->
					<div class="card swiper-slide">
						<div class="image-content"><span class="overlay"></span>
							<div class="card-image">
								<img src="<?php echo BASEURL; ?>procedimentos/imagens/lash.jpg" alt="" class="card-img">
							</div>
						</div>
						<div class="card-content">
							<h2 class="name">Lash Lifting</h2>
							<p class="description">Realce seu olhar com naturalidade, com cílios mais alongados, curvados e volumosos.</p>
							<button class="buttonc">Saber mais</button>
						</div>
					</div>
					<div class="card swiper-slide">
						<div class="image-content"><span class="overlay"></span>
							<div class="card-image">
								<img src="<?php echo BASEURL; ?>procedimentos/imagens/care.jpg" alt="" class="card-img">
							</div>
						</div>
						<div class="card-content">
							<h2 class="name">Skin Care</h2>
							<p class="description">Melhore sua autoestima com uma rotina personalizada de Skin Care, para tratar as o que mais te incomodam.</p>
							<button class="buttonc">Saber mais</button>
						</div>
					</div>
					<!-- TODOS OS CARDS ATÉ O FINAL DO SEU CÓDIGO ORIGINAL MANTIDOS -->
				</div>
			</div>
			<div class="swiper-button-next swiper-navbtn"></div>
			<div class="swiper-button-prev swiper-navbtn"></div>
			<div class="swiper-pagination"></div>
		</div>
	</div>
</section>

<section class="sobre section-light section-cor3" id="sobre">
	<div class="interface">
		<article class="itens-container">
			<div class="txti">
				<p class="txt3">C O N H E Ç A</p>
				<h2 class="txt1"><i>Larissa Moraes</i></h2>
				<p class="txt2">
					Especialista em estética avançada com abordagem profundamente humanizada e empoderadora.
					Proporciono experiências únicas e acolhedoras, focadas não apenas em resultados estéticos
					excepcionais, mas também na segurança emocional e autoestima das minhas clientes. 
					<br>
					Meu atendimento é voltado para realçar a beleza natural, transmitir clareza 
					sobre cada procedimento e criar um ambiente onde você se sinta valorizada,
					segura e pronta para alcançar sua melhor versão.  Do embelezamento do olhar à limpeza de pele, 
					garanto resultados personalizados, eficazes e esteticamente bonitos.
					<br>
					Pensada para ser uma Clínica de Autocudado, a Lunaris oferece um atendimento personalizado e humanizado.
					Acredito que cada pessoa merece um atendimento único e especial, portanto, me dedico a oferecer soluções estéticas,
					com a atenção e o cuidado que você merece.
				</p>
				<button class="buttonS">Agende agora!</button>
			</div>
			
			<div class="img-itens img-sobre">
				<img class="img-sobre" src="img/la1.jpeg" alt="">
			</div>
		</article>
	</div>
</section>

<script>
	var swiper = new Swiper(".slide-content", {
		slidesPerView: 3,
		spaceBetween: 25,
		loop: true,
		centerSlide: 'true',
		fade: 'true',
		gragCursor:'true',
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
			dynamicBullets: true,
		},
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		},
		breakpoints:{
			0: { slidesPerView: 1 },
			520: { slidesPerView: 2 },
			950: { slidesPerView: 3 },
		},
	});
</script>
<script src="<?php echo BASEURL; ?>js/swiper-bundle.min.js"></script>

<script>
	const imgBoContainer = document.getElementById('img-bo-container');
	const imgHtml = imgBoContainer.innerHTML;
	function toggleImage() {
		const screenWidth = window.innerWidth;
		if (screenWidth < 865) {
			const img = document.getElementById('laravel-img');
			if (img) img.remove();
		} else {
			if (!document.getElementById('laravel-img')) {
				imgBoContainer.innerHTML = imgHtml;
			}
		}
	}
	toggleImage();
	window.addEventListener('resize', toggleImage);

	document.addEventListener("DOMContentLoaded", function() {
		const btn = document.querySelector(".buttonS");
		const larguraFixa = 200;
		const alturaFixa = 50;
		const tamanhoFonte = 16;
		function fixButtonSize() {
			btn.style.width = larguraFixa + "px";
			btn.style.height = alturaFixa + "px";
			btn.style.fontSize = tamanhoFonte + "px";
			btn.style.padding = "10px 22px";
			btn.style.display = "inline-block";
			btn.style.flex = "0 0 auto";
		}
		fixButtonSize();
		window.addEventListener("resize", fixButtonSize);
	});
</script>

<?php include(FOOTER_TEMPLATE); ?>
