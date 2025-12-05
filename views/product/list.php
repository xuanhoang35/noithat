<?php ob_start(); ?>
<div class="space-y-6">
    <section class="glass-panel rounded-3xl shadow-xl p-5 relative overflow-hidden">
        <div class="grid lg:grid-cols-[1fr,1.2fr] gap-5 items-center">
            <div class="space-y-1">
                <p class="section-title text-slate-500">Bộ sưu tập</p>
                <h1 class="text-2xl font-bold text-slate-900">Sản phẩm nội thất & thiết bị gia dụng</h1>
                <p class="text-slate-600 text-sm">Lọc nhanh theo danh mục, giá và từ khóa để tìm sản phẩm phù hợp không gian của bạn.</p>
                <div class="text-sm text-slate-500 mt-1">Tìm thấy <strong><?php echo count($products); ?></strong> sản phẩm</div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-4 relative shadow-sm">
                <form method="get" class="space-y-3">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="relative flex-1">
                            <input id="product-search-input" type="text" name="q" value="<?php echo htmlspecialchars($keyword ?? ''); ?>" placeholder="Tìm nhanh: sofa, tủ bếp, tủ lạnh..." class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 shadow-inner" autocomplete="off">
                            <div id="product-search-suggestions" class="absolute left-0 right-0 mt-2 bg-slate-900 text-slate-100 rounded-xl shadow-2xl border border-blue-500/40 max-h-80 overflow-y-auto hidden z-20"></div>
                        </div>
                        <button class="px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow">Tìm kiếm</button>
                        <?php if (!empty($keyword ?? '')): ?>
                            <a href="<?php echo base_url('products'); ?>" class="text-blue-600 text-sm hover:underline">Xóa lọc</a>
                        <?php endif; ?>
                    </div>
                    <div class="grid gap-3 lg:grid-cols-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <select name="category" class="px-3 py-2 border rounded-lg focus:outline-none">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ((string)$categoryId === (string)$cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input name="cat_q" value="<?php echo htmlspecialchars($categorySearch ?? ''); ?>" class="px-3 py-2 border rounded-lg focus:outline-none text-sm" placeholder="Lọc danh mục">
                        </div>
                        <select name="price_sort" class="px-3 py-2 border rounded-lg focus:outline-none">
                            <option value="">Sắp xếp theo giá</option>
                            <option value="asc" <?php echo ($priceSort ?? '') === 'asc' ? 'selected' : ''; ?>>Giá thấp đến cao</option>
                            <option value="desc" <?php echo ($priceSort ?? '') === 'desc' ? 'selected' : ''; ?>>Giá cao đến thấp</option>
                        </select>
                        <select name="price_range" id="price-range" class="px-3 py-2 border rounded-lg focus:outline-none">
                            <option value="">Khoảng giá</option>
                            <option value="under-1" <?php echo ($priceRange ?? '') === 'under-1' ? 'selected' : ''; ?>>Dưới 1 triệu</option>
                            <option value="1-2" <?php echo ($priceRange ?? '') === '1-2' ? 'selected' : ''; ?>>1 - 2 triệu</option>
                            <option value="2-4" <?php echo ($priceRange ?? '') === '2-4' ? 'selected' : ''; ?>>2 - 4 triệu</option>
                            <option value="4-6" <?php echo ($priceRange ?? '') === '4-6' ? 'selected' : ''; ?>>4 - 6 triệu</option>
                            <option value="custom" <?php echo ($priceRange ?? '') === 'custom' ? 'selected' : ''; ?>>Tùy chọn</option>
                        </select>
                        <div id="custom-price-wrapper" class="grid grid-cols-2 gap-2 <?php echo ($priceRange ?? '') === 'custom' ? '' : 'hidden'; ?>">
                            <input type="number" name="price_min" class="px-3 py-2 border rounded-lg focus:outline-none" placeholder="Giá min" value="<?php echo htmlspecialchars($priceMin ?? ''); ?>">
                            <input type="number" name="price_max" class="px-3 py-2 border rounded-lg focus-outline-none" placeholder="Giá max" value="<?php echo htmlspecialchars($priceMax ?? ''); ?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 products">
        <?php foreach ($products as $p): ?>
            <?php $img = asset_url(!empty($p['image']) ? $p['image'] : 'public/assets/img/placeholder.svg'); ?>
            <?php $stock = (int)($p['stock'] ?? 0); ?>
            <div class="floating fade-border rounded-2xl shadow-sm transition p-3 flex flex-col bg-white/90">
                <div class="overflow-hidden rounded-xl mb-3 relative h-44">
                    <a href="<?php echo base_url('product/' . $p['id']); ?>">
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    </a>
                    <span class="absolute top-2 left-2 px-3 py-1 rounded-full text-xs font-semibold <?php echo $stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'; ?>">
                        <?php echo $stock > 0 ? 'Còn hàng' : 'Hết hàng'; ?>
                    </span>
                </div>
                <h6 class="text-sm font-semibold line-clamp-2 text-slate-900"><?php echo $p['name']; ?></h6>
                <p class="text-blue-700 font-bold mb-1 text-base"><?php echo number_format($p['price']); ?> đ</p>
                <p class="text-[12px] font-semibold mb-3 <?php echo $stock > 0 ? 'text-emerald-600' : 'text-red-600'; ?>">
                    <?php echo $stock > 0 ? 'Còn ' . $stock . ' SP' : 'Hàng đang về'; ?>
                </p>
                <div class="mt-auto grid grid-cols-2 gap-2">
                    <a class="h-11 w-full inline-flex items-center justify-center text-sm border rounded-lg border-slate-200 hover:border-blue-500" href="<?php echo base_url('product/' . $p['id']); ?>">Xem</a>
                    <?php if ($stock <= 0): ?>
                        <a class="h-11 w-full inline-flex items-center justify-center text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600" href="tel:0974734668">Liên hệ</a>
                    <?php else: ?>
                        <form method="post" action="<?php echo base_url('cart/add'); ?>" class="w-full">
                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/products'); ?>">
                            <button class="h-11 w-full inline-flex items-center justify-center text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Thêm giỏ</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
            <div class="col-span-full text-center text-slate-500 py-10">Không tìm thấy sản phẩm phù hợp.</div>
        <?php endif; ?>
    </div>
</div>
<script>
const productDetailBase = <?php echo json_encode(base_url('product')); ?>;
const searchEndpoint = <?php echo json_encode(base_url('products/search')); ?>;
document.addEventListener('DOMContentLoaded', function(){
    const input = document.getElementById('product-search-input');
    const list = document.getElementById('product-search-suggestions');
    const categoryInput = document.querySelector('[name="category"]');
    const rangeSelect = document.getElementById('price-range');
    const customWrapper = document.getElementById('custom-price-wrapper');
    if (rangeSelect && customWrapper) {
        const toggleCustom = () => {
            if (rangeSelect.value === 'custom') customWrapper.classList.remove('hidden');
            else customWrapper.classList.add('hidden');
        };
        toggleCustom();
        rangeSelect.addEventListener('change', toggleCustom);
    }
    if (!input || !list) return;
    let timer;
    const render = (items) => {
        if (!items || items.length === 0) {
            list.innerHTML = '<div class="p-3 text-sm text-slate-300">Không tìm thấy sản phẩm phù hợp.</div>';
            list.classList.remove('hidden');
            return;
        }
        list.innerHTML = items.map(item => `
            <a class="flex gap-3 p-2 border-b border-slate-800 last:border-none hover:bg-slate-800/70" href="${productDetailBase}/${item.id}">
                <img src="${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded">
                <div class="flex-1">
                    <p class="text-sm font-semibold leading-snug text-white" style="-webkit-line-clamp:2;-webkit-box-orient:vertical;display:-webkit-box;overflow:hidden;">${item.name}</p>
                    <p class="text-red-500 font-bold text-sm mt-1">${item.price}</p>
                </div>
            </a>
        `).join('');
        list.classList.remove('hidden');
    };
    const search = () => {
        const value = input.value.trim();
        if (!value) { list.classList.add('hidden'); list.innerHTML=''; return; }
        const cat = categoryInput ? categoryInput.value : '';
        const url = `${searchEndpoint}?q=${encodeURIComponent(value)}` + (cat ? `&category=${encodeURIComponent(cat)}` : '');
        fetch(url).then(r => r.json()).then(render).catch(() => {});
    };
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(search, 250);
    });
    input.addEventListener('focus', () => { if (list.innerHTML.trim() !== '') list.classList.remove('hidden'); });
    document.addEventListener('click', (e) => {
        if (!list.contains(e.target) && e.target !== input) {
            list.classList.add('hidden');
        }
    });
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>
