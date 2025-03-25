<?php
/**
 * Asset Details Modal View
 * This file contains the modal structure for displaying asset details
 */
?>
<!-- Asset Details Modal -->
<div class="modal fade" id="assetDetailsModal" tabindex="-1" aria-labelledby="assetDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="assetDetailsModalLabel">Détails de l'Actif</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Asset details will be loaded here via JavaScript -->
        <div class="text-center py-3" id="assetModalLoader">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2">Chargement des détails...</p>
        </div>
        <div id="assetModalContent" style="display: none;">
          <div class="row mb-4">
            <div class="col-md-6">
              <p><strong>Nom:</strong> <span id="modal-asset-name"></span></p>
              <p><strong>Catégorie:</strong> <span id="modal-asset-category"></span></p>
              <p><strong>Date d'acquisition:</strong> <span id="modal-asset-acquisition-date"></span></p>
              <p><strong>Prix d'acquisition:</strong> <span id="modal-asset-acquisition-value"></span>€</p>
            </div>
            <div class="col-md-6">
              <p><strong>Valeur actuelle:</strong> <span id="modal-asset-current-value"></span>€</p>
              <p><strong>Dernière évaluation:</strong> <span id="modal-asset-valuation-date"></span></p>
              <p><strong>Évolution:</strong> <span id="modal-asset-evolution"></span></p>
              <p id="modal-asset-location-container" style="display: none;">
                <strong>Emplacement:</strong> <span id="modal-asset-location"></span>
              </p>
            </div>
          </div>
          
          <!-- Notes Section -->
          <div id="modal-asset-notes-section" style="display: none;">
            <hr>
            <h6 class="mb-3">Notes</h6>
            <div class="p-3 border rounded bg-light">
              <span id="modal-asset-notes"></span>
            </div>
          </div>
          
          <!-- Value Evolution Chart -->
          <hr>
          <h6 class="mb-3">Évolution de la Valeur</h6>
          <div class="mb-4">
            <canvas id="modalAssetValueChart" width="400" height="200"></canvas>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-warning" id="modal-edit-asset-btn">Modifier</button>
        <button type="button" class="btn btn-danger" id="modal-delete-asset-btn">Supprimer</button>
      </div>
    </div>
  </div>
</div>
