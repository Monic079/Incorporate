<?php
// Espera $matching (resultado de MatchingController::forCv)
if(!$matching) return;

$hints = ['Skills' => MatchingController::W_SKILLS, 'Carrera' => MatchingController::W_CAREER, 'Nivel' => MatchingController::W_LEVEL];
?>
<section class="matching" id="matching_section">

    <div class="matching_header">
        <h2>Oportunidades para ti</h2>
        <div class="matching_hint">
            <?php foreach($hints as $label => $w): ?>
                <span class="hint_chip"><?= $label ?> <?= $w ?>%</span>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if(empty($matching['results'])): ?>
        <p class="matching_empty">Por ahora no hay oportunidades abiertas que comparar con este CV.</p>

    <?php else: ?>
        <div class="matching_list">
            <?php foreach($matching['results'] as $m): ?>
                <?php $tier = $m['score'] >= 75 ? 'high' : ($m['score'] >= 40 ? 'mid' : 'low'); ?>
                <div class="match_card">

                    <div class="match_top">
                        <div>
                            <h3><?= htmlspecialchars($m['title']) ?></h3>
                            <p class="match_sub">
                                <?= htmlspecialchars($m['company'] ?? 'Empresa') ?>
                                · <?= htmlspecialchars(ucfirst($m['type'])) ?>
                                · <?= htmlspecialchars(ucfirst($m['modality'])) ?>
                            </p>
                            <?php if($m['deadline']): ?>
                                <p class="match_sub">Hasta: <?= htmlspecialchars($m['deadline']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="match_pct match_<?= $tier ?>"><?= $m['score'] ?>%</div>
                    </div>

                    <div class="match_bar">
                        <div class="match_fill match_fill_<?= $tier ?>" style="width: <?= $m['score'] ?>%"></div>
                    </div>

                    <?php if(!empty($m['matched']) || !empty($m['missing'])): ?>
                        <div class="match_skills">
                            <?php foreach($m['matched'] as $s): ?>
                                <span class="match_chip match_chip_ok">✓ <?= htmlspecialchars($s) ?></span>
                            <?php endforeach; ?>
                            <?php foreach($m['missing'] as $s): ?>
                                <span class="match_chip match_chip_no"><?= htmlspecialchars($s) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="match_actions">
                        <a class="match_link" href="oportunidades_detail_view.php?id=<?= $m['id'] ?>">
                            Ver detalles
                            <span class="match_link_arrow">→</span>
                        </a>

                        <?php if($m['applied']): ?>
                            <span class="match_done">✓ Ya aplicaste</span>
                        <?php else: ?>
                            <form method="GET" action="../controllers/ApplicationController.php"
                                onsubmit="return confirm('¿Aplicar a esta oportunidad con este CV?');">
                                <input type="hidden" name="action" value="apply">
                                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                <input type="hidden" name="cv_id" value="<?= (int)$matching['cv']['id'] ?>">
                                <input type="hidden" name="back" value="cv">
                                <button type="submit" class="match_apply">Aplicar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>