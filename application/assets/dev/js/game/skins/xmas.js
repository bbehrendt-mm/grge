window.addEventListener('load',function(){

    window.setTimeout(function() {

        $('body').append($('<div />').attr('id',"snowflakeContainer").append($('<p />').addClass('snowflake').text('*')));

        var requestAnimationFrame = window.requestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.msRequestAnimationFrame;

        var transforms = ["transform",
            "msTransform",
            "webkitTransform",
            "mozTransform",
            "oTransform"];

        var transformProperty = getSupportedPropertyName(transforms);

        var snowflakes = [];

        var browserWidth;
        var browserHeight;

        var numberOfSnowflakes = 50;

        var resetPosition = false;

        function setup() {
            generateSnowflakes();
            window.addEventListener("resize", setResetFlag, false);
        }

        function getSupportedPropertyName(properties) {
            for (var i = 0; i < properties.length; i++) {
                if (typeof document.body.style[properties[i]] != "undefined") {
                    return properties[i];
                }
            }
            return null;
        }

        function Snowflake(element, radius, speed, xPos, yPos) {

            // set initial snowflake properties
            this.element = element;
            this.radius = radius;
            this.speed = speed;
            this.xPos = xPos;
            this.yPos = yPos;

            // declare variables used for snowflake's motion
            this.counter = 0;
            this.sign = Math.random() < 0.5 ? 1 : -1;

            // setting an initial opacity and size for our snowflake
            this.element.style.opacity = .1 + Math.random();
            this.element.style.fontSize = 12 + Math.random() * 50 + "px";
        }

        Snowflake.prototype.update = function () {

            // using some trigonometry to determine our x and y position
            this.counter += this.speed / 5000;
            this.xPos += this.sign * this.speed * Math.cos(this.counter) / 40;
            this.yPos += Math.sin(this.counter) / 40 + this.speed / 30;

            // setting our snowflake's position
            setTranslate3DTransform(this.element, Math.round(this.xPos), Math.round(this.yPos));

            // if snowflake goes below the browser window, move it back to the top
            if (this.yPos > browserHeight) {
                this.yPos = -50;
            }
        };

        function setTranslate3DTransform(element, xPosition, yPosition) {
            element.style[transformProperty] = "translate3d(" + xPosition + "px, " + yPosition + "px" + ", 0)";
        }

        function generateSnowflakes() {

            // get our snowflake element from the DOM and store it
            var originalSnowflake = document.querySelector(".snowflake");

            // access our snowflake element's parent container
            var snowflakeContainer = originalSnowflake.parentNode;

            // get our browser's size
            browserWidth = document.documentElement.clientWidth;
            browserHeight = document.documentElement.clientHeight;

            // create each individual snowflake
            for (var i = 0; i < numberOfSnowflakes; i++) {

                // clone our original snowflake and add it to snowflakeContainer
                var snowflakeCopy = originalSnowflake.cloneNode(true);
                snowflakeContainer.appendChild(snowflakeCopy);

                // set our snowflake's initial position and related properties
                var initialXPos = getPosition(50, browserWidth);
                var initialYPos = getPosition(50, browserHeight);
                var speed = 5+Math.random()*40;
                var radius = 4+Math.random()*10;

                // create our Snowflake object
                var snowflakeObject = new Snowflake(snowflakeCopy,
                    radius,
                    speed,
                    initialXPos,
                    initialYPos);
                snowflakes.push(snowflakeObject);
            }

            // remove the original snowflake because we no longer need it visible
            snowflakeContainer.removeChild(originalSnowflake);

            // call the moveSnowflakes function every 30 milliseconds
            moveSnowflakes();
        }

        function moveSnowflakes() {
            for (var i = 0; i < snowflakes.length; i++) {
                var snowflake = snowflakes[i];
                snowflake.update();
            }

            // Reset the position of all the snowflakes to a new value
            if (resetPosition) {
                browserWidth = document.documentElement.clientWidth;
                browserHeight = document.documentElement.clientHeight;

                for (var i = 0; i < snowflakes.length; i++) {
                    var snowflake = snowflakes[i];

                    snowflake.xPos = getPosition(50, browserWidth);
                    snowflake.yPos = getPosition(50, browserHeight);
                }

                resetPosition = false;
            }

            requestAnimationFrame(moveSnowflakes);
        }

        function getPosition(offset, size) {
            return Math.round(-1*offset + Math.random() * (size+2*offset));
        }

        function setResetFlag(e) {
            resetPosition = true;
        }

        setup();

    }, 1000);

});