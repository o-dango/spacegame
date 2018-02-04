"use strict";

/*Sources:
http://www.html5gamedevs.com/topic/23672-weapon-plugin/
https://phaser.io/examples/v2/arcade-physics/asteroids-movement
https://leanpub.com/html5shootemupinanafternoon/read#leanpub-auto-enemy-sprite-group
sounds: https://opengameart.org/content/512-sound-effects-8-bit-style
sharebuttons: https://simplesharingbuttons.com/#intro
loader image: https://icons8.com/cssload/en/horizontal-bars/2
*/

/*TO-DO:
-Asteroid hitbox
-Asteroids spawns around world
-Make asteroids not to spawn on top of player
-Multiplayer
-Users, done
-Better space physics
-Highscores, done
-Start ja pause menu
-More powerups
-Bigger game field
-Camera follows the player
-Explosion animation
-More sound effects
*/

/*global Phaser*/
/*global $*/

var canvasSupported = !!window.HTMLCanvasElement;

if (canvasSupported == false) { //if canvas is not supported
	$("#gamearea").append("<p>Your browser/device does not support the game :(</p>");
} 

else if (window.innerWidth < 768) {
	console.log("Game not starting, too small device!");
	$("#gamearea").append("<p>Your browser/device does not support the game :(</p>");
}

else { /*create game if canvas is supported*/

	var game = new Phaser.Game(800, 600, Phaser.CANVAS, 'gamearea', { preload: preload, create: create, update: update, render: render });
	
}

var player_name;
	
function preload() {
	//game.canvas.id = 'gamearea'; //set gamearea id
	/*load game sprites*/
	game.load.image('bullet', 'assets/sprites/bullet.png');
    game.load.spritesheet('ship', 'assets/sprites/ship.png', 64, 64, 8);
	game.load.image('background', 'assets/sprites/background.png');
	game.load.image('paused', 'assets/sprites/paused.png');
	game.load.image('asteroid', 'assets/sprites/asteroid.png');
	game.load.spritesheet('poweruplogo', 'assets/sprites/beampickup.png', 40, 40, 4);
	game.load.spritesheet('heart', 'assets/sprites/heart.png', 40, 40, 4);
	game.load.spritesheet('beam', 'assets/sprites/beam.png', 32, 32, 4);
	game.load.spritesheet('restart', 'assets/sprites/restart.png', 512, 128, 3);
	/*load game audio*/
	game.load.audio('weapon1', 'assets/sounds/weapon1.wav');
	
	$.get('getplayer.php', {requested: 'username'}, function (data) {
	    player_name = data;
	    console.log("Now playing: " + player_name);
	});
}
/*variables*/
var player;
var weapon1;
var weapon1_fx;
var weapon2;
var bullets;
/*input keys*/
var cursors;
var fireButton;
var beamButton;
var pauseButton;
/*texts*/
var beamText;
var healthText;
var scoreText;
var pauseText;
var reset;
/*countable*/
var powerup;
var beams = 1;
var powerups;
var heart;

var spawnAllowed = false;
var asteroids;
/*final score*/
var destroyed = 0;

function create() {
	
	/*creates the game from scratch*/
	
	/*render and physics options for game*/
	game.renderer.clearBeforeRender = false;
    game.renderer.roundPixels = true;
	game.physics.startSystem(Phaser.Physics.ARCADE);

	game.add.sprite(0, 0, 'background');

	/*creates weapons, powerups, asteroids and player*/
	generateWeapons();
    powerups = game.add.group();
	powerups.enableBody = true;
	generatePlayer();
	generateAsteroids();

	/*define controls for the game*/
	cursors = this.input.keyboard.createCursorKeys();
    fireButton = this.input.keyboard.addKey(Phaser.KeyCode.X);
    fireButton.onDown.add(fireBlast, this);
	beamButton = this.input.keyboard.addKey(Phaser.KeyCode.Z);
	pauseButton = this.input.keyboard.addKey(Phaser.Keyboard.ESC);
    pauseButton.onDown.add(togglePause, this);

	/*define text*/
	beamText = game.add.text(5,5, "Beams: " + beams, {fontSize: "32px;", fill: "#ffff"});
	healthText = game.add.text(5,20, "Health: " + player.health, {fontSize: "32px;", fill: "#ffff"});
	scoreText = game.add.text(5,35, "Destroyed: " + destroyed, {fontSize: "32px;", fill: "#ffff"});
	pauseText = game.add.sprite(game.world.centerX, game.world.centerY, 'paused');
	reset = game.add.button(game.world.centerX, game.world.centerY, 'restart', resetGame, this, 2, 0, 1);
	
	/*when pausing*/
	pauseText.anchor.set(0.5);
	pauseText.visible = false;
	/*when reseting*/
	reset.anchor.set(0.5);
	reset.visible = false;

}

function update() {
	
	/*defines what happens every update cycle in game*/

	/*define what happens when objects collide etc*/
	game.physics.arcade.overlap(player, powerups, collectPowerup, null, this);
	game.physics.arcade.collide(player, asteroids, hitPlayer, null, this);
	game.physics.arcade.overlap(player, asteroids, hitPlayer, null, this);
	game.physics.arcade.collide(asteroids, asteroids);
	game.physics.arcade.overlap(weapon1.bullets, asteroids, hitAsteroid, null, this);
	game.physics.arcade.overlap(weapon2.bullets, asteroids, hitAsteroid, null, this);


	/*define movement*/
	if (cursors.up.isDown) {
	        game.physics.arcade.accelerationFromRotation(player.rotation, 300, player.body.acceleration);
			player.animations.play('rocket'); /*play animation if moving forward*/
	    }
    else {
    	/*stop acceleration and animation if forward-key is not pressed*/
        player.body.acceleration.set(0);
		player.animations.stop();
		player.frame = 0;
    }

	/*for player turning*/
    if (cursors.left.isDown) {
        player.body.angularVelocity = -300;
    }
    else if (cursors.right.isDown) {
        player.body.angularVelocity = 300;
    }
    else {
        player.body.angularVelocity = 0;
    }

	/*if special weapon button is pressed*/
	if (beamButton.isDown) {
			if(beams > 0) { /*check if there's any beams left*/
				beams -= 1;
				beamText.text = "Beams: " + beams;
				game.time.events.repeat(10, 200, function() {
					weapon2.fire();
					if (cursors.left.isDown) {
				        player.body.angularVelocity = -30;
				    }
				    else if (cursors.right.isDown) {
				        player.body.angularVelocity = 30;
				    }}, this);
		}
    }

	/*define how often asteroids spawn and when they can start spawning*/
	if (this.game.time.totalElapsedSeconds() >= 5) {
		spawnAllowed = true;
	}

	if (spawnAllowed == true) {
		if (game.nextEnemyAt < game.time.now && asteroids.countDead() > 0) {

			generateAsteroid(); /*spawn an asteroid*/

	    }
	}


	/*wrap player and bullets*/
	game.world.wrap(player, 16);
    bullets.forEachExists(screenWrap, this);
	asteroids.forEachExists(screenWrap, this);

}

function render() {
	/*Debugging*/
    //asteroids.forEachExists(game.debug.body, game.debug);
	//game.debug.body(player);

}


function collectPowerup(player, powerup) {
	
	/*Define what different powerups do*/

	if(powerup.key == 'poweruplogo') {
		beams += 1;
		powerup.destroy();
		beamText.text = "Beams: " + beams;
	}

	else if(powerup.key == 'heart') {
		player.heal(1)
		powerup.destroy();
		healthText.text = "Health: " + player.health;
	}

}

function hitAsteroid(bullet, asteroid) {
	
	/*Define what happens when different objects hits an asteroid*/

	var damage = 0;

	if (bullet.key == 'bullet') {
		damage = 1; /*bullet does one damage to asteroid*/
		game.physics.arcade.accelerationFromRotation(asteroid.rotation, 100, asteroid.body.acceleration);
	}
	else if (bullet.key == 'beam') {
		damage = 3; /*special weapon does three damage*/
		game.physics.arcade.accelerationFromRotation(asteroid.rotation, 500, asteroid.body.acceleration);
	}

	bullet.kill(); /*kill the bullet*/
	asteroid.damage(damage); /*update asteroid's health*/

	if (asteroid.health <= 0) { /*if asteroid's health is zero or under*/
		destroyed += 1; /*increase score by one*/
		scoreText.text = "Destroyed: " + destroyed; /*update onscreen text*/
        generatePowerups(asteroid); /*asteroid might drop powerups :)*/

	}

}

function hitPlayer(player, asteroid) {

	/*Define what happens when player hits object*/

	var damage = 1;

	asteroid.kill(); /*asteroid dissappears but doesn't increase score :)*/
	player.damage(damage); /*update player's health*/
	healthText.text = "Health: " + player.health; /*update onscreen text*/

	if (player.health <= 0) { /*end game if player's health hits zero*/
		endGame();
	}

}

function endGame() { 
	
	/*end game :(*/

	reset.visible = true;
	game.input.keyboard.enabled = false;
	highscoreWindow(); /*WIP*/

}

function resetGame() {

	/*resets game so it can start again, yay*/
	asteroids.removeAll();
	generateAsteroids();
	player.revive();
	player.reset(game.world.centerX, game.world.centerY);
	reset.visible = false;
	game.input.keyboard.enabled = true;
	destroyed = 0;
	beams = 1;
	player.heal(3);
	healthText.text = "Health: " + player.health;
	beamText.text = "Beams: " + beams;
	scoreText.text = "Destroyed: " + destroyed;


}

function generateWeapons() {
	
	/*creates weapons for player*/

	bullets = game.add.group(); /*bullets in one group*/
	game.physics.arcade.enable(bullets);
    bullets.enableBody = true;
    bullets.physicsBodyType = Phaser.Physics.ARCADE;

	/*primary weapon*/
	weapon1 = game.add.weapon(100, 'bullet');
	weapon1_fx = game.add.audio('weapon1');
	weapon1_fx.volume = 0.2;
	weapon1.bulletClass = bullets;
	weapon1.bulletAngleOffset = 90;
	weapon1.bulletKillType = Phaser.Weapon.KILL_WORLD_BOUNDS;
	weapon1.bulletInheritSpriteSpeed = true;
	weapon1.fireRate = 50;
    weapon1.bulletWorldWrap = true;

	/*special weapon*/
	weapon2 = game.add.weapon(100, 'beam');
	weapon2.bulletClass = bullets;
	weapon2.setBulletFrames(0, 4, true);
	weapon2.bulletAngleOffset = 90;
	weapon2.bulletKillType = Phaser.Weapon.KILL_WORLD_BOUNDS;
	weapon2.bulletSpeed = 900;
	weapon2.fireRate = 20;

}

function generatePowerups(asteroid) {
	
	/*creates powerups*/

    var rand = game.rnd.realInRange(0, 1);

    if (rand < 0.1) { /*10% possibility to drop special weapon*/
        powerup = powerups.create(asteroid.body.x, asteroid.body.y, 'poweruplogo');
        var flash = powerup.animations.add('flash');
        powerup.animations.play('flash', 12, true); /*play animation*/
        console.log("Asteroid dropped beam!");
    }

    else if (rand >=0.1 && rand < 0.2) { /*10% possibility to drop extra health*/
        heart = powerups.create(asteroid.body.x, asteroid.body.y, 'heart');
        var pump = heart.animations.add('pump');
        heart.animations.play('pump', 4, true); /*play animation*/
        console.log("Asteroid dropped health!");
    }

    else { /*80% possibility to drop nothing :)*/
	console.log("Asteroid dropped nothing!");
    }


}

function generateAsteroids() {
	
	/*creates asteroids*/

	asteroids = game.add.group();
	game.physics.arcade.enable(asteroids);
	asteroids.enableBody = true;
	asteroids.physicsBodyType = Phaser.Physics.ARCADE;
	asteroids.createMultiple(20, 'asteroid'); /*max number of asteroids is 20*/
    asteroids.setAll('anchor.x', 0.5);
    asteroids.setAll('anchor.y', 0.5);
    //asteroids.setAll('outOfBoundsKill', true);
    //asteroids.setAll('checkWorldBounds', true);
	game.nextEnemyAt = 0;
    game.enemyDelay = 2000; /*delay between asteroid spawns*/

}

function generatePlayer() {
	
	/*creates player*/

	player = game.add.sprite(game.world.centerX, game.world.centerY, 'ship');
	var rocket = player.animations.add('rocket', [1,2,3,4,5,6,7,8], 24, true);
	player.anchor.set(0.5);
	game.physics.arcade.enable(player);
	player.body.drag.set(70);
    player.body.maxVelocity.set(200);
	player.body.setSize(50, 50, 10, 10);
	player.health = 3; /*player starting health*/
	player.maxHealth = 3; /*player max health*/
	/*weapons follow player sprite*/
	weapon1.trackSprite(player, 30, 0, true);
	weapon2.trackSprite(player, 30, 0, true);

}

function generateAsteroid() {
	
	/*creates one asteroid*/

	game.nextEnemyAt = game.time.now + game.enemyDelay; /*time delay for next asteroid*/
	var asteroid = asteroids.getFirstExists(false);
	asteroid.reset(game.rnd.integerInRange(20, 780), 0);
	asteroid.body.velocity.y = game.rnd.integerInRange(30, 100); /*random speed*/
	asteroid.body.angularVelocity = game.rnd.integerInRange(-50, 50); /*random rotation*/
	var rand = game.rnd.realInRange(0.5, 1);
	asteroid.scale.setTo(rand*0.7, rand*0.7); /*random size*/
	asteroid.body.bounce.set(0.8, 0.8);
	asteroid.health = 3 + rand*1.5; /*random health*/

}

function screenWrap (sprite) {
	
	/*gameworld wrap*/

    if (sprite.x < 0) {
        sprite.x = game.width;
    }
    else if (sprite.x > game.width) {
        sprite.x = 0;
    }

    if (sprite.y < 0) {
        sprite.y = game.height;
    }
    else if (sprite.y > game.height) {
        sprite.y = 0;
    }

}



function togglePause() {
	
	/*Toggles pause on/off when pause button is pressed
	if variable's value is false the value is changed to true and vice versa*/

	pauseText.visible = (pauseText.visible) ? false : true;
	game.paused = (game.paused) ? false : true;

}

function fireBlast() {
	
	/*shoots bullet and plays sound for primary weapon*/
	
	weapon1.fire(); /*fires bullet*/
    weapon1_fx.play(); /*plays weapon sound*/
}

function highscoreWindow() {
	
	/*handler for high score window, if player is not logged in player
	can input the name of choice. Logged in players scores will be saved
	with that name.*/
	
	$(document).ready(function() {
    	console.log("ready!");
    	$("#dialog").dialog({modal: true, closeOnEscape: false, dialogClass: "no-close"}); /*open dialog*/
		$(".tips").text("Test").removeClass("ui-state-highlight");
		if(player_name !== "Anonymous") { /*if player have logged in*/
			$("input[name=player]").val(player_name).attr("readonly", true);
		} else { /*if player have not logged in*/
			$("input[name=player]").attr("placeholder", "Anonymous")
		}
		$("#score").text("Score: " + destroyed); /*add score*/
		/*listeners*/
		submitScore(); /*for button*/
		enterListener(); /*for enter*/
	});
}

function checkPlayer(input) { 
	
	/*checks if player's name is valid for database*/
	
    console.log(input);
    if(input.length == 0) { /*if there's no input*/
        updateTips("Namefield is empty!");
        return false;
    }

    else if (/^[a-zA-ZåöäÅÖÄ]+$/.test(input)) { /*only letters allowed*/
        $("#dialog").removeClass("ui-state-error");
        return true;
    }

    else { /*if name breaks any rules*/
        updateTips("Name can only include numbers and letters!");
        return false;
    }

}

function updateTips(info) {
	
	/*updates tips for invalid input*/
	
  $(".tips").text(info).addClass("ui-state-highlight");
  setTimeout(function() {
    $(".tips").removeClass("ui-state-highlight", 1500);
  }, 500 );

}

function sendScore() {
	
	/*sends data to php page where database is updated. This function also
	updates highscore box without whole page refreshing if something was 
	added to database*/
	
	player_name = $("input[name=player]").val();
	if (checkPlayer(player_name) === true) { /*if player's name is valid*/
        $.ajax({
          type: 'POST',
          url: 'score.php',
          data: {player: player_name, score: destroyed}, /*data we want to store*/
          success: function() {
	          console.log("Score submitted");
	          console.log(player_name + ": " + destroyed);
	          $("#dialog").dialog("close"); /*close window*/
	          $('#highscores').load('game.php #highscores'); /*loads new highscores*/
            },
          error: function() {
        	 console.log("Submitting failed!");
        	}
    	});
	}
}

function submitScore() {
	
	/*listens for submit button in score form and triggers score submit*/
	
	$(document).ready(function() {
        $("#submitbtn").click(function(event) {
    		sendScore();
    	});
	});
}

function enterListener() {
	
	/*listens enter presses and triggers score submit*/
	
    $(document).ready(function() {
        $(document).keypress(function (event) {
            var key = event.which;
            if(key == 13) {
                if($('input[name=player]').val().length !== 0) {
                    sendScore();
                }
                return false;
            }
        });
    });
}

