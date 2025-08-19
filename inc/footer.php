		<footer class="custom-footer py-5">
			<div class="container">
				<div class="row gy-4 text-center text-md-start">

				  <!-- Contato -->
			
					<div id="contato" class="col-md-4">
						<h5 class="fw-bold mb-3" style="color:var(--cor1)">Contato</h5>
						<p class="d-flex align-items-center justify-content-center justify-content-md-start">
							<i class="fas fa-phone me-2"></i> (13) 99639-4246
						</p>
						<p class="d-flex align-items-center justify-content-center justify-content-md-start">
							<i class="fas fa-envelope me-2"></i> lmoraes.farma@gmail.com
						</p>
						<p class="d-flex align-items-center justify-content-center justify-content-md-start">
							<i class="fas fa-map-marker-alt me-2"></i> R. Dr. Carvalho de Mendonça, 93 - Santos/SP
						</p>
					</div>

				  <!-- Institucional -->
					<div class="col-md-4">
						<h5 class="fw-bold mb-3" style="color:var(--cor1)">Institucional</h5>
						<p><a href="#sobre" class="footer-link">Sobre nós</a></p>
						<p><a href="#" class="footer-link">Trabalhe conosco</a></p>
						<p><a href="#" class="footer-link">Política de privacidade</a></p>
					</div>


				  <!-- Localização -->
					<div class="col-md-4">
						<h5 class="fw-bold mb-3" style="color:var(--cor1)">Onde estamos</h5>
						<div class="map-responsive rounded overflow-hidden shadow-sm mb-3">
							 <iframe
								src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3662.119149492492!2d-46.3221574844457!3d-23.95782198475861!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce063b40313d9b%3A0x5bf4f8f66813ca12!2sRua%20Dr.%20Carvalho%20de%20Mendon%C3%A7a%2C%2093%20-%20Encruzilhada%2C%20Santos%20-%20SP%2C%2011030-290!5e0!3m2!1spt-BR!2sbr!4v1716887925421!5m2!1spt-BR!2sbr"
								width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy">
							</iframe> 
						</div>
					</div>
				</div>
				
				<div class="social-icons text-center text-md-start">
					<a href="#" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
					<a href="#" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
					<a href="#" class="social-icon" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
					<a href="#" class="social-icon" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
				</div>


				<div class="text-center pt-4 mt-4 border-top">
				  <?php $data = new Datetime("now", new DateTimeZone("America/Sao_Paulo")) ?>
				  <p class="mb-0 small">&copy;2025 - <?php echo $data->format("Y"); ?> Lunaris. Todos os direitos reservados.</p>
				</div>
			</div>
		</footer>
		
	</body>
</html>
