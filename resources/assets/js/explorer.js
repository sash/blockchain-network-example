import App from "./components/Explorer/App";
import Explorer from "./components/Explorer/Explorer";
//import Example from "./components/Example";
//import * as ReactDOM from "react-router-dom";

require('./bootstrap');

console.log('im the explorer');

new Explorer(document.getElementById('app'),$('#app').data('nodes').split(',').reduce(function(stack, el) {
    const spl = el.split(':');
    stack[spl[0]] = $('#app').data('host') + ":" + spl[1];
    return stack;
}, {}));
