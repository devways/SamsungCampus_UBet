<div class="row ticket-bet" style="clear: both"> 
            <p class="col-md-4" style="margin-left: 52px">Team One  /  Team Two</p>
            <p class="col-md-7">team one --- team two</p>
</div>
<?php foreach($events as $key => $values): ?>
    <?php if($values['winner_event'] === null): ?>
    <div class="row ticket-bet" style="clear: both"> 
            <img src="<?php if(file_exists('C:/wamp64/www/ScoolProjects/Depot/B.E.T/assets/img/' . str_replace(' ', '_', $values['category_event']) . ".png")){
                    echo '/ScoolProjects/Depot/B.E.T/assets/img/' . str_replace(' ', '_', $values['category_event']) . ".png";
                } else {
                    echo '/ScoolProjects/Depot/B.E.T/assets/img/default.jpg';
                } ?>" alt="" class="img-responsive mini-logo col-md-1"/>
            <div class="col-md-4"> <?= $values['team_one_event'] . ' / ' . $values['team_two_event']; ?> </div>
            <div class="col-md-7"> <?= ' ----- '.$values['coteOne']. ' ------------ ' .$values['coteTwo'].' ----- '; ?> </div>
    </div>
    <?php endif; ?>
<?php  endforeach; ?>