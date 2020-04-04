const button = document.querySelector(".header__menu-icon");
const nav = document.querySelector(".nav");
const menuOptions = document.querySelectorAll(".navigation__option");
const menuIconLines = document.querySelectorAll(".menu-line");
const tl = new TimelineMax({paused: true});

const menuAnimation = tl
.addLabel("start")
.to(nav, 0.65, {
    'transform': 'translateX(0)',
    'height': 'auto',
    'opacity': '1',
    ease: Power1.easeInOut
})
.staggerTo(menuOptions, 0.3, {
    'transform': 'translateX(0)',
    'opacity': '1'
}, 0.15, 0.4)
.to(menuIconLines[1], 0.2, {'opacity': 0}, "start")
.to(menuIconLines[0], 0.3, {
    'transform': 'translateX(0px) translateY(12px) rotate(-45deg)',
}, "start")
.to(menuIconLines[2], 0.3, {
    'transform': 'translateX(0) translateY(-12px) rotate(45deg)'
}, "start");


let counter = 1;
button.addEventListener("click", () => {
    counter++;
    counter % 2 === 0 ? menuAnimation.play() : menuAnimation.reverse();
});