<div class="container col-md-offset-4">
<div class="col-md-5">
    <div class="form-area">  
        <form role="form" action='/ScoolProjects/Depot/B.E.T/user/Panel' method="post">
        <br style="clear:both">
                    <h3 class="col-md-offset-4">Event</h3>
    				<div class="form-group">
						<input type="text" class="form-control" id="name" name="event" placeholder="Event Name" required>
					</div>
                    <div class="form-group">
                        <select class="form-control" name="category">
                            <option>overwatch</option>
                            <option>paladin</option>
                            <option>call of duty</option>
                            <option>counter strike</option>
                            <option>league of legends</option>
                            <option>dota2</option>
                            <option>smite</option>
                            <option>hereos of the storm</option>
                            <option>hearthstone</option>
                            <option>krosmaga</option>
                            <option>clash royal</option>
                            <option>starcraft ii</option>
                            <option>fifa</option>
                        </select>
                    </div>
					<div class="form-group">
						<input type="text" class="form-control" id="email" name="team_one" placeholder="Team One" required>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="mobile" name="team_two" placeholder="Team Two" required>
					</div>
                    <div class="form-group">
                        <input type="date" name="date_begin" value="">
                        <input type="time" name="hour_begin" value="">                    
                    </div>
                    <div class="form-group">
                        <input type="date" name="date_end" value="">
                        <input type="time" name="hour_end" value="">                    
                    </div>
            
        <button type="submit" id="submit" name="submit" class="btn btn-primary pull-right">Submit Form</button>
        </form>
    </div>
</div>
</div>