// js/hls-player.js
document.addEventListener('DOMContentLoaded', function() {
    const videos = document.querySelectorAll('video');
    
    videos.forEach(video => {
        const source = video.querySelector('source')?.src;
        if (!source) return;
        
        const extension = source.split('.').pop().toLowerCase();
        const nativeFormats = ['mp4', 'webm', 'ogg'];
        
        // Si le format n'est pas supporté nativement
        if (!nativeFormats.includes(extension) && Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(source);
            hls.attachMedia(video);
            
            // Gestion des erreurs HLS
            hls.on(Hls.Events.ERROR, function(event, data) {
                console.error('HLS error:', data);
                if(data.fatal) {
                    switch(data.type) {
                        case Hls.ErrorTypes.NETWORK_ERROR:
                            hls.startLoad();
                            break;
                        case Hls.ErrorTypes.MEDIA_ERROR:
                            hls.recoverMediaError();
                            break;
                        default:
                            console.error('Unrecoverable HLS error');
                    }
                }
            });
        }
    });
});