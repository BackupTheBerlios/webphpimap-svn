var scriptaculous_queue = new Array();
var ajax_queue = new Array();
var effect_queue = new Array();
var fck_queue = new Array();
var oFCK = new Array();
var oFCKform = new Array();
var navi_vis = false;
var navi_first = 0;

function AjaxGet(layer,site,praeeffect,posteffect){
     if(layer==null || layer=="undefined") layer = getElement(null);
     if(layer!=null){
          ajax_queue[layer.id] = true;
          ScriptEffect(praeeffect,layer);
          ajaxcaller = new Ajax.Updater(layer,site,{
               method: 'get',
               asynchronous:true,
               evalScripts:true,
               onSuccess:function(request) {
                    ajax_queue[layer.id] = false;
               },
               onFailure:function(request) {
                    layer.innerHTML = "Verbindungsfehler: ";
                    layer.innerHTML += request.responseText;
                    ajax_queue[layer.id] = false;
               },
               onComplete:function(request) {
                    ScriptEffect(posteffect,layer);
               }
          });
          chk = false;
     } else
          chk = true;
          return chk;
}

function AjaxPost(layer,site,form,praeeffect,posteffect){
     if(layer==null || layer=="undefined") layer = getElement(null);
     if(layer!=null){
          ajax_queue[layer.id] = true;
          ScriptEffect(praeeffect,layer);
          ajaxcaller = new Ajax.Updater(layer,site,{
               parameters:Form.serialize(document.forms[form]),
               asynchronous:true,
               evalScripts:true,
               onSuccess:function(request) {
                    ajax_queue[layer.id] = false;
               },
               onFailure:function(request) {
                    layer.innerHTML = "Verbindungsfehler: ";
                    layer.innerHTML += request.responseText;
                    ajax_queue[layer.id] = false;
               },
               onComplete:function(request) {
                    ScriptEffect(posteffect,layer);
               }
          });
          chk = false;
     } else
          chk = true;
          return chk;
}

function getElement(id){
     var item = document.getElementById(id);
     return item;
}

function getParentById(id,layer){
     if(layer.id==id) return layer;
     else if(layer.parentNode==null) return null;
     else return getParentById(id,layer.parentNode);
}

function getParentDiv(layer){
     if(layer.tagName!="div" && layer.tagName!="DIV" && layer.parentNode!=null) return getParentDiv(layer.parentNode);
     else if(layer.parentNode==null) { return null; }
     else { return layer; }
}

function ScriptEffect(effect,layer){
     if(effect=='appear') {
          new Effect.Appear(layer);
     } else if(effect=='fade') {
          new Effect.Fade(layer);
     } else if(effect=='pulsate') {
          new Effect.Pulsate(layer);
     } else if(effect=='highlight') {
          new Effect.Highlight(layer);
     } else if(effect=='fold') {
          new Effect.Fold(layer);
     } else if(effect=='puff') {
          new Effect.Puff(layer);
     } else if(effect=='grow') {
          new Effect.Grow(layer);
     } else if(effect=='blindup') {
          new Effect.BlindUp(layer);
     } else if(effect=='blinddown') {
          new Effect.BlindDown(layer);
     } else if(effect=='scale') {
          new Effect.Scale(layer,150);
     } else if(effect=='scale2') {
          new Effect.Scale(layer,66.66);
     } else if(effect=='slideup') {
          new Effect.SlideUp(layer);
     } else if(effect=='slidedown') {
          new Effect.SlideDown(layer);
     }
}