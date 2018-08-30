$(document).ready(function(){

 $("#button-expert").click(function(){
  $("#expert-menu").toggleClass( "open" );
  if ($("#supervisor-menu").innerWidth()>0)
   { $("#supervisor-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu2").innerWidth()>0)
   { $("#supervisor-menu2").toggleClass( "open" ); }
  if ($("#admin-menu").innerWidth()>0)
   { $("#admin-menu").toggleClass( "open" ); }
 });
 $("#button-expert-h").click(function(){
  $("#expert-menu").toggleClass( "open" );
  if ($("#supervisor-menu").innerWidth()>0)
   { $("#supervisor-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu2").innerWidth()>0)
   { $("#supervisor-menu2").toggleClass( "open" ); }
  if ($("#admin-menu").innerWidth()>0)
   { $("#admin-menu").toggleClass( "open" ); }
 });

 $("#button-supervisor").click(function(){
  $("#supervisor-menu").toggleClass( "open" );
  if ($("#expert-menu").innerWidth()>0)
   { $("#expert-menu").toggleClass( "open" ); }
  if ($("#admin-menu").innerWidth()>0)
   { $("#admin-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu2").innerWidth()>0)
   { $("#supervisor-menu2").toggleClass( "open" ); }
 });
 $("#button-supervisor-h").click(function(){
  $("#supervisor-menu").toggleClass( "open" );
  if ($("#expert-menu").innerWidth()>0)
   { $("#expert-menu").toggleClass( "open" ); }
  if ($("#admin-menu").innerWidth()>0)
   { $("#admin-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu2").innerWidth()>0)
   { $("#supervisor-menu2").toggleClass( "open" ); }
 });

 $("#button-supervisor2").click(function(){
  $("#supervisor-menu2").toggleClass( "open" );
  if ($("#expert-menu").innerWidth()>0)
   { $("#expert-menu").toggleClass( "open" ); }
  if ($("#admin-menu").innerWidth()>0)
   { $("#admin-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu").innerWidth()>0)
   { $("#supervisor-menu").toggleClass( "open" ); }
 });
 $("#button-supervisor-h2").click(function(){
  $("#supervisor-menu2").toggleClass( "open" );
  if ($("#expert-menu").innerWidth()>0)
   { $("#expert-menu").toggleClass( "open" ); }
  if ($("#admin-menu").innerWidth()>0)
   { $("#admin-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu").innerWidth()>0)
   { $("#supervisor-menu").toggleClass( "open" ); }
 });

 $("#button-admin").click(function(){
  $("#admin-menu").toggleClass( "open" );
  if ($("#expert-menu").innerWidth()>0)
   { $("#expert-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu").innerWidth()>0)
   { $("#supervisor-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu2").innerWidth()>0)
   { $("#supervisor-menu2").toggleClass( "open" ); }
 });
 $("#button-admin-h").click(function(){
  $("#admin-menu").toggleClass( "open" );
  if ($("#expert-menu").innerWidth()>0)
   { $("#expert-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu").innerWidth()>0)
   { $("#supervisor-menu").toggleClass( "open" ); }
  if ($("#supervisor-menu2").innerWidth()>0)
   { $("#supervisor-menu2").toggleClass( "open" ); }
 });

});

$(document).click(function(e) { 
 if ( (e.target.className != 'menu-button expert') && (e.target.className != 'menu-button supervisor') && (e.target.className != 'menu-button supervisor2') && (e.target.className != 'menu-button admin'))
 {
  if ( e.target.className != 'menu-button expert' )
   if ($("#expert-menu").innerWidth()>0)
    { $("#expert-menu").toggleClass( "open" ); }
  
  if ( e.target.className != 'menu-button supervisor' )
   if ($("#supervisor-menu").innerWidth()>0)
    { $("#supervisor-menu").toggleClass( "open" ); }

  if ( e.target.className != 'menu-button supervisor2' )
   if ($("#supervisor-menu2").innerWidth()>0)
    { $("#supervisor-menu2").toggleClass( "open" ); }

  if ( e.target.className != 'menu-button admin' )
   if ($("#admin-menu").innerWidth()>0)
    { $("#admin-menu").toggleClass( "open" ); }
 }
});
