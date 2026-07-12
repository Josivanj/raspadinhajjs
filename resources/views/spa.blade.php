<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Raspadinha') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root { --gold:#f4b942; --green:#12c985; --panel:#161a24; --muted:#99a1b3; }
        * { box-sizing:border-box; }
        body { margin:0; color:#fff; background:#080a0f; font-family:Inter,Arial,sans-serif; }
        header { position:sticky; top:0; z-index:5; display:flex; align-items:center; justify-content:space-between; padding:16px max(20px,5vw); background:rgba(8,10,15,.94); border-bottom:1px solid #252a36; backdrop-filter:blur(12px); }
        .brand { display:flex; align-items:center; gap:10px; font-size:22px; font-weight:900; color:var(--gold); }
        .brand span { width:38px; height:38px; display:grid; place-items:center; border-radius:12px; color:#111; background:linear-gradient(135deg,#ffd56a,#e49317); }
        nav { display:flex; gap:10px; }
        button,.button { border:0; border-radius:10px; padding:11px 18px; font-weight:800; cursor:pointer; text-decoration:none; color:#fff; background:#252b38; }
        .primary { color:#07130f; background:var(--green); }
        .hero { min-height:390px; display:flex; align-items:center; padding:55px max(20px,7vw); background:radial-gradient(circle at 80% 20%,rgba(244,185,66,.25),transparent 35%),linear-gradient(120deg,#111725,#090b11); }
        .hero-copy { max-width:650px; }
        h1 { margin:0 0 18px; font-size:clamp(38px,7vw,76px); line-height:.98; }
        h1 em { color:var(--gold); font-style:normal; }
        .hero p { max-width:560px; color:#c5cad5; font-size:18px; line-height:1.6; }
        .hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:28px; }
        main { padding:36px max(20px,5vw) 70px; }
        .section-title { display:flex; align-items:end; justify-content:space-between; margin-bottom:20px; }
        h2 { margin:0; font-size:28px; }
        .section-title small { color:var(--muted); }
        .games { display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); gap:18px; }
        .game { overflow:hidden; border:1px solid #252a36; border-radius:16px; background:var(--panel); transition:.2s; }
        .game:hover { transform:translateY(-5px); border-color:var(--gold); }
        .game img { display:block; width:100%; aspect-ratio:1/1; object-fit:cover; background:#222834; }
        .game div { padding:14px; }
        .game strong { display:block; margin-bottom:5px; }
        .game small { color:var(--muted); }
        .scratch-section { padding:42px max(20px,5vw) 8px; }
        .scratch-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:18px; }
        .scratch-card { overflow:hidden; border:1px solid #343b4b; border-radius:18px; background:linear-gradient(145deg,#1e2430,#11151e); }
        .scratch-card img { width:100%; height:175px; display:block; object-fit:cover; background:linear-gradient(135deg,#f4b942,#7d4d00); }
        .scratch-card-body { padding:17px; }
        .scratch-card h3 { margin:0 0 7px; }
        .scratch-meta { display:flex; justify-content:space-between; margin:14px 0; color:var(--gold); font-weight:800; }
        .scratch-card button { width:100%; background:var(--green); color:#07130f; }
        .account { display:none; align-items:center; gap:10px; }
        .balance { color:var(--gold); font-weight:900; }
        .scratch-board { display:grid; grid-template-columns:repeat(3,1fr); gap:9px; margin:18px 0; }
        .scratch-item { min-height:92px; display:grid; place-items:center; padding:8px; text-align:center; border:2px dashed #5b6477; border-radius:12px; background:#252b38; cursor:pointer; }
        .scratch-item.revealed { border-style:solid; border-color:var(--gold); background:#10141c; }
        .scratch-item img { width:55px; height:55px; object-fit:contain; }
        .empty-state { grid-column:1/-1; color:var(--muted); padding:25px; text-align:center; border:1px dashed #343b4b; border-radius:14px; }
        footer { padding:25px; text-align:center; color:#737b8d; border-top:1px solid #20242f; }
        dialog { width:min(92vw,420px); color:#fff; border:1px solid #343b4b; border-radius:18px; background:#151923; }
        dialog::backdrop { background:rgba(0,0,0,.75); }
        dialog form { display:grid; gap:13px; }
        input { width:100%; padding:13px; color:#fff; border:1px solid #343b4b; border-radius:9px; outline:none; background:#0c0f15; }
        .dialog-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
        .dialog-head h3 { margin:0; }
        .close { padding:7px 11px; }
        #message { min-height:20px; color:#ff7777; font-size:14px; }
        @media(max-width:600px){ header{padding:12px 16px}.brand{font-size:17px}.hero{min-height:330px;padding:45px 20px}nav .button{display:none} }
    </style>
</head>
<body>
<header>
    <div class="brand"><span>★</span>{{ config('app.name', 'Raspadinha') }}</div>
    <nav>
        <a class="button" href="#jogos">Jogos</a>
        <button class="guest-action" onclick="loginModal.showModal()">Entrar</button>
        <button class="primary guest-action" onclick="registerModal.showModal()">Criar conta</button>
        <div class="account" id="account"><span class="balance" id="balance">R$ 0,00</span><button onclick="logout()">Sair</button></div>
    </nav>
</header>

<section class="hero">
    <div class="hero-copy">
        <h1>Raspe, jogue e <em>concorra!</em></h1>
        <p>Entre na diversão com raspadinhas e jogos rápidos. Crie sua conta para acessar a plataforma.</p>
        <div class="hero-actions">
            <button class="primary" onclick="registerModal.showModal()">Começar agora</button>
            <a class="button" href="#jogos">Ver jogos</a>
        </div>
    </div>
</section>

<section class="scratch-section" id="raspadinhas">
    <div class="section-title"><h2>Raspadinhas</h2><small>Raspe e descubra seu prêmio</small></div>
    <div class="scratch-grid" id="scratchGrid"><div class="empty-state">Carregando raspadinhas...</div></div>
</section>

<main id="jogos">
    <div class="section-title"><h2>Jogos em destaque</h2><small>Escolha seu favorito</small></div>
    <div class="games" id="gameGrid">
        @foreach ([
            ['tigre.webp','Fortune Tiger'], ['rabbit.webp','Fortune Rabbit'], ['mouse.webp','Fortune Mouse'],
            ['ox.webp','Fortune Ox'], ['ganesha.webp','Ganesha Gold'], ['gates.webp','Gates of Olympus'],
            ['tree.webp','Árvore da Fortuna'], ['gold.webp','Golden Wins'], ['fruit.png','Fruit Party'],
            ['sugar.png','Sugar Rush'], ['dog.png','Lucky Dog'], ['big.png','Big Win']
        ] as [$image, $name])
            <article class="game" onclick="requireLogin()">
                <img src="{{ asset('assets/images/games') }}/{{ $image }}" alt="{{ $name }}" loading="lazy">
                <div><strong>{{ $name }}</strong><small>Clique para jogar</small></div>
            </article>
        @endforeach
    </div>
</main>

<dialog id="loginModal">
    <div class="dialog-head"><h3>Entrar</h3><button class="close" onclick="loginModal.close()">×</button></div>
    <form id="loginForm">
        <input name="email" type="email" placeholder="Seu e-mail" required>
        <input name="password" type="password" placeholder="Sua senha" required>
        <div id="message"></div>
        <button class="primary" type="submit">Entrar</button>
    </form>
</dialog>

<dialog id="scratchModal">
    <div class="dialog-head"><h3 id="scratchTitle">Raspadinha</h3><button class="close" onclick="scratchModal.close()">×</button></div>
    <p id="scratchStatus">Confirme para jogar.</p>
    <div class="scratch-board" id="scratchBoard"></div>
    <button class="primary" id="scratchPlayButton" onclick="playSelectedScratch()">Jogar agora</button>
</dialog>

<dialog id="registerModal">
    <div class="dialog-head"><h3>Criar conta</h3><button class="close" onclick="registerModal.close()">×</button></div>
    <form id="registerForm">
        <input name="cpf" inputmode="numeric" placeholder="CPF (11 números)" minlength="11" maxlength="14" required>
        <input name="phone" inputmode="tel" placeholder="Celular com DDD" required>
        <input name="email" type="email" placeholder="Seu e-mail" required>
        <input name="password" type="password" placeholder="Senha (mínimo 6 caracteres)" minlength="6" required>
        <div id="registerMessage"></div>
        <button class="primary" type="submit">Criar minha conta</button>
    </form>
</dialog>

<footer>© {{ date('Y') }} {{ config('app.name', 'Raspadinha') }}. Todos os direitos reservados.</footer>
<script>
const api = async (url, options = {}) => {
    const token = localStorage.getItem('auth_token');
    const headers = {...(options.headers || {}), Accept: 'application/json'};
    if (token) headers.Authorization = `Bearer ${token}`;
    const response = await fetch(url, {...options, headers});
    const result = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(result.message || result.error || Object.values(result).flat().join(' ') || 'Não foi possível concluir.');
    return result;
};

const requireLogin = () => {
    if (!localStorage.getItem('auth_token')) return loginModal.showModal();
    document.getElementById('jogos').scrollIntoView({behavior: 'smooth'});
};

const money = value => Number(value || 0).toLocaleString('pt-BR', {style:'currency', currency:'BRL'});
let selectedScratch = null;
const scratchRoutes = [
    {max:1.99, route:'raspadinha'}, {max:2.99, route:'raspadinha-cinco'},
    {max:5.99, route:'raspadinha-dez'}, {max:25.99, route:'raspadinha-milhao'},
    {max:50.99, route:'raspadinha-make'}, {max:60.99, route:'raspadinha-6'},
    {max:80.99, route:'raspadinha-7'}, {max:100.99, route:'raspadinha-8'},
    {max:Infinity, route:'raspadinha-9'}
];

function syncAuth(user = null) {
    const logged = !!localStorage.getItem('auth_token');
    document.querySelectorAll('.guest-action').forEach(el => el.style.display = logged ? 'none' : 'inline-block');
    document.getElementById('account').style.display = logged ? 'flex' : 'none';
    if (logged) loadWallet();
}

async function loadWallet() {
    try {
        const result = await api('/api/profile/wallet');
        const wallet = result.wallet || result.data || result;
        const total = Number(wallet.balance || 0) + Number(wallet.balance_withdrawal || 0) + Number(wallet.balance_bonus || 0);
        document.getElementById('balance').textContent = money(total);
    } catch (error) {
        if (/token|unauth/i.test(error.message)) logout(false);
    }
}

async function logout(callApi = true) {
    if (callApi) await api('/api/auth/logout', {method:'POST'}).catch(() => {});
    localStorage.removeItem('auth_token');
    syncAuth();
}

function openScratch(card) {
    if (!localStorage.getItem('auth_token')) return loginModal.showModal();
    selectedScratch = card;
    document.getElementById('scratchTitle').textContent = card.name;
    document.getElementById('scratchStatus').textContent = `Valor da jogada: ${money(card.price)}. Prêmio máximo: ${money(card.max_prize)}.`;
    document.getElementById('scratchBoard').innerHTML = Array.from({length:9}, () => '<div class="scratch-item">?</div>').join('');
    document.getElementById('scratchPlayButton').disabled = false;
    scratchModal.showModal();
}

async function playSelectedScratch() {
    if (!selectedScratch) return;
    const button = document.getElementById('scratchPlayButton');
    const status = document.getElementById('scratchStatus');
    button.disabled = true;
    status.textContent = 'Processando sua raspadinha...';
    try {
        const route = scratchRoutes.find(item => Number(selectedScratch.price) <= item.max).route;
        const result = await api(`/api/profile/${route}`, {method:'POST'});
        document.getElementById('scratchBoard').innerHTML = result.items.map(item => `
            <div class="scratch-item revealed">${item.image ? `<img src="${item.image}" alt="">` : ''}<strong>${item.name}</strong></div>`).join('');
        status.textContent = result.win ? `Parabéns! Você ganhou ${result.winningItemName || money(result.value)}.` : 'Não foi desta vez. Tente novamente!';
        await loadWallet();
    } catch (error) {
        status.textContent = error.message;
        button.disabled = false;
    }
}

async function loadScratchCards() {
    const grid = document.getElementById('scratchGrid');
    try {
        const result = await api('/api/raspadinhas');
        const cards = result.data || [];
        if (!cards.length) throw new Error('Nenhuma raspadinha ativa no momento.');
        grid.innerHTML = cards.map((card, index) => `<article class="scratch-card">
            <img src="${card.image ? (card.image.startsWith('http') ? card.image : '/storage/' + card.image.replace(/^\//,'')) : '/assets/images/FortuneTiger.webp'}" alt="${card.name}" loading="lazy">
            <div class="scratch-card-body"><h3>${card.name}</h3><small>${card.description || 'Raspe e concorra a prêmios.'}</small>
            <div class="scratch-meta"><span>${money(card.price)}</span><span>Até ${money(card.max_prize)}</span></div>
            <button onclick='openScratch(${JSON.stringify(card).replaceAll("'", "&#39;")})'>Raspar agora</button></div></article>`).join('');
    } catch (error) { grid.innerHTML = `<div class="empty-state">${error.message}</div>`; }
}

document.getElementById('loginForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = document.getElementById('message');
    message.textContent = 'Entrando...';
    const data = Object.fromEntries(new FormData(event.target));
    try {
        const result = await api('/api/auth/login', {method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)});
        localStorage.setItem('auth_token', result.access_token);
        syncAuth(result.user);
        message.style.color = '#12c985';
        message.textContent = 'Login realizado! Escolha um jogo.';
        setTimeout(() => loginModal.close(), 700);
    } catch (error) { message.textContent = error.message; }
});

document.getElementById('registerForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = document.getElementById('registerMessage');
    message.textContent = 'Criando conta...';
    try {
        const data = Object.fromEntries(new FormData(event.target));
        const registration = await api('/api/auth/register', {method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)});
        localStorage.setItem('auth_token', registration.access_token);
        syncAuth(registration.user);
        message.style.color = '#12c985';
        message.textContent = 'Conta criada com sucesso!';
        setTimeout(() => registerModal.close(), 900);
    } catch (error) { message.style.color = '#ff7777'; message.textContent = error.message; }
});

async function openGame(id) {
    if (!localStorage.getItem('auth_token')) return loginModal.showModal();
    try {
        const result = await api(`/api/games/single/${id}`);
        if (result.gameUrl) window.location.href = result.gameUrl;
        else throw new Error(result.error || 'Jogo indisponível.');
    } catch (error) { alert(error.message); }
}

async function loadGames() {
    try {
        const result = await api('/api/casinos/games');
        const games = result.games?.data || [];
        if (!games.length) return;
        document.getElementById('gameGrid').innerHTML = games.map(game => `
            <article class="game" onclick="openGame(${Number(game.id)})">
                <img src="${game.cover || '/favicon.ico'}" alt="${String(game.game_name || 'Jogo').replaceAll('"','&quot;')}" loading="lazy">
                <div><strong>${game.game_name || 'Jogo'}</strong><small>Clique para jogar</small></div>
            </article>`).join('');
    } catch (error) { console.error('Falha ao carregar jogos:', error.message); }
}

loadGames();
loadScratchCards();
syncAuth();
</script>
</body>
</html>
