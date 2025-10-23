<!-- Modal -->
<div class="modal fade" id="delete-cli-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card-custom">
            <div class="modal-body text-center">
                <h3 class="modal-title-custom">Excluir Cliente</h3>
                <p class="modal-text">Deseja realmente excluir este Cliente? Esta ação não pode ser desfeita.</p>

                <div class="modal-actions d-flex justify-content-center gap-3">
                    <a type="button" class="buttonc gap" id="confirm" href="#">
                        <i class="fa-solid fa-check"></i> Sim, excluir
                    </a>
                    <button type="button" class="buttonc cancel" style="background:#ccc;color:#333" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ensure modal overlay sits above footer and page content */
.modal-overlay {
  position: fixed !important;
  top: 0; left: 0; right: 0; bottom: 0;
  display: none;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.5);
  z-index: 2000;
}
.modal-overlay .modal-card {
  z-index: 2001;
  position: relative;
  background: #fff;
  color: #222;
  padding: 20px 22px;
  width: 100%;
  max-width: 520px;
  border-radius: 12px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.25);
  pointer-events: auto;
}
.modal-overlay .modal-card h3 { margin-top: 0; color: #73213d; }
.modal-overlay .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 14px; }
.modal-overlay .modal-actions .buttonc { padding: 10px 14px; border-radius: 8px; }
.modal-overlay .modal-actions form { margin: 0; }
</style>