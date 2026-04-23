import './bootstrap';
window.Echo.private('App.Models.User.1')
    .notification((notification) => {
        console.log(notification);
    });