<?php $i = 0; ?>
<?php foreach($events as $key => $values): ?>
    <div class="row ticket"> 
            <div class="col-md-1 col-md-offset-1">
                <figure>
                    <img src="<?php echo '/ScoolProjects/Depot/B.E.T/assets/img/' . str_replace(' ', '_', $values['category_event']) . ".png"; ?>" 
                    alt="logo de category" class="img-responsive logo">
                    <figcaption class="text-center"><?= $values['category_event'] ?></figcaption>
                </figure>
            </div>
            <div class="col-md-6 ticket-container">
                <div class="row title">
                    <img src="<?php echo '/ScoolProjects/Depot/B.E.T/assets/img/' . str_replace(' ', '_', $values['category_event']) . ".png"; ?>"
                     alt="logo de category" class="img-responsive col-md-2 mini-logo">
                    <p class="col-md-10">
                        <?= $values['name_event']; ?>
                    </p>
                </div>
                <div class="row">
                    <figure class="col-md-3 col-md-offset-1">
                        <img src='<?php if(file_exists("C:\\wamp64\\www\\ScoolProjects\\Depot\\B.E.T\\assets\\img\\" . str_replace(' ', '_', $values['team_one_event']) . ".png")) {
                            echo "/ScoolProjects/Depot/B.E.T/assets/img/" . str_replace(' ', '_', $values['team_one_event']) . ".png";
                        } else {
                            echo '/ScoolProjects/Depot/B.E.T/assets/img/default.jpg';
                        } ?>' alt="logo d'equipe" class="img-responsive middle-logo col-md-offset-3"/>
                        <figcaption class="text-center"><a><?= $values['team_one_event']; ?></a></figcaption>
                    </figure>
                    <figure class="col-md-4">
                        <img src='/ScoolProjects/Depot/B.E.T/assets/img/versus.png' alt="logo de versus" class="img-responsive middle-logo col-md-offset-4"/>
                        <figcaption class="text-center"><small class="small"><?= $values['date_begin_event'] . '  -  ' . $values['date_end_event'] ?></small></figcaption>
                    </figure>
                    <figure class="col-md-3">
                        <img src="<?php if(file_exists("C:\\wamp64\\www\\ScoolProjects\\Depot\\B.E.T\\assets\\img\\" . str_replace(' ', '_', $values['team_two_event']) . ".png")){
                            echo '/ScoolProjects/Depot/B.E.T/assets/img/' . str_replace(' ', '_', $values['team_two_event']) . ".png";
                        } else {
                            echo '/ScoolProjects/Depot/B.E.T/assets/img/default.jpg';
                        } ?>" alt="logo d'equipe" class="img-responsive middle-logo col-md-offset-3"/>
                        <figcaption class="text-center"><a><?= $values['team_two_event']; ?></a></figcaption>
                    </figure>
                </div>
                <div class="row">
                    <form id='<?= $values['id_event']; ?>' action=# method="post">
                        <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['admin'] === '1' && $values['winner_event'] === null): ?>
                        <button type="submit" name="win" value="<?= $values['team_one_event'].';'.$values['id_event']; ?>" class="col-md-1 col-md-offset-1" >team one win</button>
                        <button type="submit" name="win" value="equality; <?=$values['id_event'];?>" class="col-md-1 col-md-offset-1">match null</button>
                        <button type="submit" name="win" value="<?=$values['team_two_event'].';'.$values['id_event'];?>" class="col-md-1 col-md-offset-1">team two win</button>
                        <?php elseif($values['winner_event'] === null): ?>
                        <button type="submit" class="col-md-1 col-md-offset-1">+</button><input type="number" name="team_one" value="" class="col-md-2">
                        <button type="submit" class="col-md-1">+</button><input type="number" name="null" value="" class="col-md-3">
                        <button type="submit" class="col-md-1">+</button><input type="number" name="team_two" value="" class="col-md-2">
                        <input type="text" style="display:none" name="id_event" value='<?= $values['id_event']; ?>'>
                        <?php else: ?>
                        <?php if($values['winner_event'] === $values['team_one_event']): ?>
                        <p>Win</p>
                        <?php else: ?>
                        <p>Loose</p>
                        <?php endif; ?>
                        <?php if($values['winner_event'] !== $values['team_one_event'] && $values['winner_event'] !== $values['team_two_event'] && $values['winner_event'] !== null): ?>
                        <p>Win</p>
                        <?php else: ?>
                        <p>Loose</p>
                        <?php endif; ?>
                        <?php if($values['winner_event'] === $values['team_two_event']): ?>
                        <p>Win</p>
                        <?php else: ?>
                        <p>Loose</p>
                        <?php endif; ?>
                        <?php endif; ?>                      
                    </form>
                </div>
            </div>
            <div class="col-md-2">
                <?php if(isset($_SESSION['auth']) && $_SESSION['auth']['admin'] === '1'): ?>
                <button type="submit" form='<?= $values['id_event']; ?>' value='<?= $values['id_event']; ?>' name="delete"  style="height: 142.5px; width: 150px">DELETE</button>
                <?php elseif($values['winner_event'] === null): ?>
                <button type="submit" form='<?= $values['id_event']; ?>' value='<?= $values['id_event']; ?>' name="bet"  style="height: 142.5px; width: 150px">BET</button>
                <?php endif; ?>
            </div>
    </div>
<?php  endforeach; ?>