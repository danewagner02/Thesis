/**
 * Practical 3
 * RW334 Prac Memos
 * Stellenbosch University
 *
 **/


$(document).ready(function() {
    /* Mobile Menu */
    if ($(".client").length > 0) {
        $(".menuToggle").on("click", function () {
            $(".menu .wrapper").toggleClass("open");
        });
    }
    /* Home Page - Top Products */
    if ($(".client.home").length > 0) {
        window.setTimeout(function () {
            $(".topSatellitess .satellitesImages .satellitesImage:first-child").addClass("hidden");
            window.setInterval(function () {
                var rotatingProduct = $(".topSatellitess .satellitesImages .satellitesImage:first-child").detach();
                $(rotatingProduct).removeClass("hidden");
                $(".topSatellitess .SatellitesImages .satellitesImage:first-child").addClass("hidden");
                rotatingProduct.appendTo(".topSatellitess .satellitesImages");
            }, 3000);
        }, 3000);
    }
    /* Sat Page - Pagination */
    if ($(".client.satellites").length > 0) {
        var current = 1;
        var total = 1;
        var skip = 0;
        var limit = 12;
        var getSats = function () {
            var res = $.ajax({
                dataType: "json",
                url: "http://localhost/thesis/satellites.php?limit="+limit+"&skip="+skip,
                success: function (data) {
                    current = ((skip / limit) | 0) + 1;
                    total = ((data.total / limit ) | 0) + 1;
                    $(".pageNumber").html("Page " + current + " of " + total);
                    $(".satellitesGallery").html("");
                    for (var i in data.satellites) {
                        $(  '<a class="imageWrapper" href="satellite.html?id=' + data.satellites[i].id + '">' +
                            '    <img class="satellitesImage" src="' + data.satellites[i].url + '" />' +
                            '    ' + data.satellites[i].name.substring(0,34) +
                            '</a>').appendTo(".satellitesGallery");
                    }
                    if (current <= 1) {
                        $(".pager .arrowLeft").prop("disabled", true);
                    } else {
                        $(".pager .arrowLeft").prop("disabled", false);
                    }
                    if (data.total <= skip + limit) {
                        $(".pager .arrowRight").prop("disabled", true);
                    } else {
                        $(".pager .arrowRight").prop("disabled", false);
                    }
                },
                crossDomain: true
            });
        };
        getSats();
        $(".pager .arrowLeft").on("click", function () {
            skip = skip - limit;
            if (skip < 0) {
                skip = 0;
            }
            getSats();
        });
        $(".pager .arrowRight").on("click", function () {
            skip = skip + limit;
            getSats();
        });
    }
    /* SAT Page - Image Gallery */
    if ($(".client.satellite").length > 0) {
        var satelliteId = window.location.href.split("id=")[1];
        console.log(satelliteId);
        if (satelliteId) {
            satelliteId = "'" + parseInt(satelliteId) + "'";
        }
        if (!satelliteId || satelliteId == "'-1'") {
            console.error("Bad Product Id");
            return;
        }
        var existingOrder = document.cookie.split("order=")[1];
        if (!existingOrder || existingOrder.indexOf(satelliteId) === -1) {
            $(".orderButton").text("Add to Order");
        } else {
            $(".orderButton").text("Remove from Order");
        }
        $(".orderButton").on("click", function () {
            var existingOrder = (document.cookie.split("order=")[1] || "");
            console.log(existingOrder);
            var expiryDate = new Date(Date.now() + 86400000); // 24 hours forward
            console.log(expiryDate);
            var newOrder;
            if (existingOrder.indexOf(satelliteId) === -1) {
                // Add satellite to order
                if (existingOrder != "") {
                    newOrder = existingOrder + "," + satelliteId;
                } else {
                    newOrder = ""+satelliteId;
                }
                $(".orderButton").text("Remove from Order");
            } else {
                // Remove satellite from order
                newOrder = existingOrder.split(satelliteId);
                newOrder = (newOrder[0] + newOrder[1]).replace(",,",",");
                if (newOrder.charAt(newOrder.length - 1) == ',') {
                    newOrder = newOrder.substring(0, newOrder.length - 1);
                }
                if (newOrder.charAt(0) == ',') {
                    newOrder = newOrder.substring(1);
                }
                $(".orderButton").text("Add to Order");
            }
            document.cookie="order=" + newOrder + "; expires=" + expiryDate.toUTCString() + "; path=/";
        });
        $(".imageWrapper").on("click", function (e) {
            var src = $(e.target).find("img").context.src;
            $(  '<div class="galleryShadow">' +
                '   <div class="galleryShadowCenter">' +
                '       <img class="galleryShadowImage" src="' + src + '" />' +
                '   </div>' +
                '</div>').appendTo("body");
            $(".galleryShadow").on("click", function () {
                $(".galleryShadow").remove();
            });
            e.preventDefault();
            return false;
        });
    }
    /* Order Page - Validation and Item Quantity */
    if ($(".client.order").length > 0) {
        $("#orderForm").on("submit", function (e) {
            $(".notification").remove();
            var firstname = $(".detailInput.firstname").val();
            firstname = firstname.match(/^[a-z]{1}([a-z]{1,}|-{1}[a-z]{1}){1,}$/i);
            var lastname = $(".detailInput.lastname").val();
            lastname = lastname.match(/^[a-z]{1}([a-z]{1,}|-{1}[a-z]{1}){1,}$/i);
            var email = $(".detailInput.email").val();
            email = email.match(/^[a-z0-9]{1,}@[a-z0-9]{1,}(\.[a-z0-9]{2,}){1,}$/i);
            var phone = $(".detailInput.phone").val();
            phone = phone.match(/^(\+|[0-9]{1})[0-9]{2}\s?[0-9]{3}\s?[0-9]{4}$/i);
            var msg = "Please complete your ";
            if (!firstname || firstname.length <= 0) {
                msg += "firstname, ";
                $(".detailInput.firstname").addClass("error");
            }
            if (!lastname || lastname.length <= 0) {
                msg += "lastname, ";
                $(".detailInput.lastname").addClass("error");
            }
            if (!email || email.length <= 0) {
                msg += "email address, ";
                $(".detailInput.email").addClass("error");
            }
            if (!phone || phone.length <= 0) {
                msg += "phone number, ";
                $(".detailInput.phone").addClass("error");
            }
            if (msg != "Please complete your ") {
                msg = msg.substring(0, msg.length - 2) + " details to submit your order";
                var and = msg.lastIndexOf(",");
                if (and !== -1) {
                    msg = msg.substring(0, and + 1) + " and " + msg.substring(and + 1);
                }
                $('<div class="notification red">' + msg + '</div>').prependTo(".content .wrapper");
                e.preventDefault();
                return false;
            }
        });
        $(".detailInput").on("focus", function (e) {
            $(e.target).removeClass("error");
            return true;
        });
        $(".orderButton.left").on("click", function (e) {
            var orderNumber = $(e.target).siblings(".orderNumber");
            var val = orderNumber.val();
            val = parseInt(val);
            if (val === -1) {
                orderNumber.addClass("error");
            } else if (val === 1) {
                $(e.target).closest(".orderItemRow").remove();
            } else {
                orderNumber.val(val - 1);
                orderNumber.removeClass("error");
            }
            e.preventDefault();
            return false;
        });
        $(".orderButton.right").on("click", function (e) {
            var orderNumber = $(e.target).siblings(".orderNumber");
            var val = orderNumber.val();
            val = parseInt(val);
            if (val === -1) {
                orderNumber.addClass("error");
            }
            orderNumber.val(val + 1);
            orderNumber.removeClass("error");
            e.preventDefault();
            return false;
        });
    }
});


/**
 * EOF
 **/
