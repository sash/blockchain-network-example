import Coordinator from "./components/Wallet/Coordinator";

require('./bootstrap');

//
// console.log(Wallet.getInstatance(), Wallet.hasStorage());
// Wallet.new('000000', '000000');
// console.log(Wallet.getInstatance().getMnemonic(), Wallet.hasStorage());
// Wallet.lock()
// console.log(Wallet.getInstatance(), Wallet.hasStorage())
// Wallet.unlock('000000');
// console.log(Wallet.getInstatance().getMnemonic(), Wallet.hasStorage());
// Wallet.restore("pigeon lab pizza end verb urban express away crucial garment scout equal", '000000', '000000');
// console.log(Wallet.getInstatance().getMnemonic(), Wallet.hasStorage());
// Wallet.forget();
// console.log(Wallet.getInstatance(), Wallet.hasStorage());

new Coordinator(document.getElementById('app'),$('#app').data('nodes').split(',').reduce(function(stack, el) {
    const spl = el.split(':');
    stack[spl[0]] = $('#app').data('host') + ":" + spl[1];
    return stack;
}, {}), $('#app').data('explorer'));