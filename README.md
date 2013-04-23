Mono-log Player
=============

A simple scrapper to create a playlist of [Mono-log.org](http://mono-log.org) website and play it with Speakker html5 player.

## How to use

The first part requires to add the html5 audio player in your website, in this case we are using Speakker for the nice
support of playlists. Speakker can be downloaded from [speakker.com](http://speakker.com) (the script is already in the example folder).

Load jQuery
```
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
```

Load Speakker
```
<script src="speakker-big-1.2.24r221.min.js"></script>
```

Initialize speakker
```
<script type="text/javascript">
  $(document).ready(function() {
		projekktor('.projekktor');
	});
</script>
```

Place the player somewhere betweet your 'body' tags.
Make sure that the source of the 'monologPlaylistServer.php' is matches the one in your server (next step).
```
<audio class="projekktor speakker light">
  <source src="monologPlaylistServer.php" type="application/json"/>
</audio>
```

Upload the monologPlaylistServer.php to your server (capable of executing php scripts)
The first time the player can take 30 seconds to load because it will create a 'database' (json playlist)
