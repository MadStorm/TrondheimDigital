//To desktop Version script

function createCookie(a,b,c,d){if(c){var e=new Date();e.setTime(e.getTime()+(c*24*60*60*1000));var f="; expires="+e.toGMTString()}else var f="";if(d){var d="; path="+d}else var d="; path=/";document.cookie=a+"="+b+f+d}

function readCookie(a){var b=a+"=";var d=document.cookie.split(';');for(var i=0;i<d.length;i++){var c=d[i];while(c.charAt(0)==' ')c=c.substring(1,c.length);if(c.indexOf(b)==0)return c.substring(b.length,c.length)}return null}

function eraseCookie(a){createCookie(a,"",-1)}

function toDeskTop(){if(jQuery('#to-desktop').length){jQuery('#to-desktop a').click(function(e){disableMobile=readCookie('disableMobile');if(disableMobile&&disableMobile!='false'){createCookie('disableMobile',false,365)}else{createCookie('disableMobile',true,365)}window.location.href=window.location.href.replace(window.location.hash,'');return false})}else{eraseCookie('disableMobile')}}

jQuery(function(){toDeskTop()});