/* js/main.js */
document.getElementById('shareButton')?.addEventListener('click', function() {
    if (navigator.share) {
        navigator.share({
            title: 'Rejoignez notre site de paris sportifs!',
            text: 'Inscrivez-vous via mon lien de parrainage pour gagner des bonus.',
            url: window.location.href
        }).then(() => {
            console.log('Merci pour le partage!');
        }).catch(console.error);
    } else {
        alert("Le partage n'est pas supporté sur ce navigateur. Veuillez copier manuellement le lien.");
    }
});
