/*
 Template Name: Aplomb - Bootstrap 4 Admin Dashboard
 Author: Mannatthemes
 Website: www.mannatthemes.com
 File: Rating init
 */
$(function(){$("input.check").on("change",function(){alert("Rating: "+$(this).val())}),$(".rating-tooltip").rating({extendSymbol:function(t){$(this).tooltip({container:"body",placement:"bottom",title:"Rate "+t})}}),$(".rating-tooltip-manual").rating({extendSymbol:function(){var t;$(this).tooltip({container:"body",placement:"bottom",trigger:"manual",title:function(){return t}}),$(this).on("rating.rateenter",function(n,i){t=i,$(this).tooltip("show")}).on("rating.rateleave",function(){$(this).tooltip("hide")})}}),$(".rating").each(function(){$('<span class="badge badge-info"></span>').text($(this).val()||" ").insertAfter(this)}),$(".rating").on("change",function(){$(this).next(".badge").text($(this).val())});$("#element").emotionsRating({emotionSize:36,bgEmotion:"happy",emotions:["angry","disappointed","meh","happy","inLove"],color:"gold"})});