<?php
// includes/match_modals.php
?>

<!-- Modal Ajout Match -->
<div class="modal fade" id="addMatchModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="add_match.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau Match</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Équipe 1</label>
                        <input type="text" name="team1" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Équipe 2</label>
                        <input type="text" name="team2" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Date du match</label>
                        <input type="datetime-local" name="event_date" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Cote Équipe 1</label>
                            <input type="number" step="0.01" name="odds_team1" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Cote Nul</label>
                            <input type="number" step="0.01" name="odds_draw" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Cote Équipe 2</label>
                            <input type="number" step="0.01" name="odds_team2" class="form-control" required>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Édition Match -->
<div class="modal fade" id="editMatchModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="update_match.php">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier Match</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Les champs sont remplis par JavaScript -->
                    <div class="mb-3">
                        <label>Équipe 1</label>
                        <input type="text" id="team1" name="team1" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Équipe 2</label>
                        <input type="text" id="team2" name="team2" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Date du match</label>
                        <input type="datetime-local" id="event_date" name="event_date" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Cote Équipe 1</label>
                            <input type="number" step="0.01" id="odds_team1" name="odds_team1" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Cote Nul</label>
                            <input type="number" step="0.01" id="odds_draw" name="odds_draw" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Cote Équipe 2</label>
                            <input type="number" step="0.01" id="odds_team2" name="odds_team2" class="form-control" required>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                </div>
                <div class="mb-3">
    <label>Catégorie</label>
    <select name="category" class="form-select" required>
        <option value="football">Football</option>
        <option value="basketball">Basketball</option>
        <option value="tennis">Tennis</option>
        <option value="autres">Autres</option>
    </select>
</div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>