<?php
// Espera la variable $matching (resultado de MatchingController::forOpportunity)
if(!$matching) return;
?>
<section class="matching" id="matching_section">

    <div class="matching_header">
        <h2>Matching</h2>
        <span class="matching_hint">Criterios usados: <?= htmlspecialchars(implode(', ', $matching['criteria']) ?: 'ninguno') ?></span>
    </div>

    <?php if(empty($matching['criteria'])): ?>
        <p class="matching_empty">Esta oportunidad no tiene skills, carreras ni nivel definidos, así que no se puede calcular el matching.</p>

    <?php elseif(empty($matching['results'])): ?>
        <p class="matching_empty">Aún no hay CVs para comparar.</p>

    <?php else: ?>
        <div class="matching_list">
            <?php foreach($matching['results'] as $m): ?>
                <?php
                $tier = $m['score'] >= 75 ? 'high' : ($m['score'] >= 40 ? 'mid' : 'low');
                ?>
                <div class="match_card">

                    <div class="match_top">
                        <div>
                            <h3><?= htmlspecialchars($m['name']) ?></h3>
                            <p class="match_sub">
                                <?= htmlspecialchars($m['career'] ?? 'Sin carrera') ?>
                                <?php if($m['level']): ?> · <?= htmlspecialchars(ucfirst($m['level'])) ?><?php endif; ?>
                                <?php if($m['applied']): ?> <span class="match_applied">Ya aplicó</span><?php endif; ?>
                            </p>
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

                    <a class="match_link" href="cv_detail_view.php?id=<?= $m['cv_id'] ?>">Ver CV →</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>