$(function(){
  console.log("読み込みスタート");
  var $good = $('.btn-good'),
              goodPostId;//投稿ID
  $good.on('click',function(e){
    e.stopPropagation();
    var $this = $(this);

    //  カスタム属性(postid)に格納された投稿ID取得
    goodPostID = $this.parents('.post').data('postid');
    $.ajax({
      type:'POST',
      url:'ajaxGood.php', //  post送信を受け取るphpファイル
      data:{ postID: goodPostId}  // {キー:投稿ID}
    }).done(function(data){
      console.log('Ajax Success');

      // いいねの総数を表示
      $this.children('span').html(data);
      // いいね取り消しのスタイル
      $this.children('i').toggleClass('far'); //空洞ハート
      // いいね押した時のスタイル
      $this.children('i').toggleClass('fas'); //塗りつぶしハート
      $this.children('i').toggleClass('active');
      $this.toggleClass('active');
    }).fail(function(msg){
      console.log('Ajax Error');
    });
  });
});