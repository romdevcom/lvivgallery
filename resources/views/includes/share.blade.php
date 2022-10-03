@php $link = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] @endphp
<div class="sg-social">
    <span class="sg-social-button"></span>
    <div class="sg-social-pop">
        <a href="https://www.addtoany.com/add_to/facebook?linkurl={{$link}}&amp;linkname=" target="_blank"><img src="https://static.addtoany.com/buttons/facebook.svg" width="32" height="32" alt="facebook icon"></a>
        <a href="https://www.addtoany.com/add_to/twitter?linkurl={{$link}}&amp;linkname=" target="_blank"><img src="https://static.addtoany.com/buttons/twitter.svg" width="32" height="32" alt="twitter icon"></a>
        <a href="https://www.addtoany.com/add_to/email?linkurl={{$link}}&amp;linkname=" target="_blank"><img src="https://static.addtoany.com/buttons/email.svg" width="32" height="32" alt="email icon"></a>
        <a href="https://www.addtoany.com/add_to/telegram?linkurl={{$link}}&amp;linkname=" target="_blank"><img src="https://static.addtoany.com/buttons/telegram.svg" width="32" height="32" alt="telegram icon"></a>
        <a href="https://www.addtoany.com/add_to/copy_link?linkurl={{$link}}&amp;linkname=" target="_blank"><img src="https://static.addtoany.com/buttons/link.svg" width="32" height="32" alt="link icon"></a>
        <a href="https://www.addtoany.com/add_to/whatsapp?linkurl={{$link}}&amp;linkname=" target="_blank"><img src="https://static.addtoany.com/buttons/whatsapp.svg" width="32" height="32" alt="whatsapp icon"></a>
    </div>
</div>