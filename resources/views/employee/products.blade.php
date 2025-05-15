@extends('employee.main')

@section('title', 'POS | ByteVault')

@section('content')
    <div class="main-container">
        <!-- Product Side (Left, 70%) -->
        <div class="left-section">
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <h2 style="margin: 0;" id="section-title">Categories</h2>
                <button id="switch-btn" style="background: none; border: none; cursor: pointer; margin-left: 10px;">
                    <span class="material-icons-outlined" style="font-size: 24px; color: var(--color-2);">swap_horiz</span>
                </button>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <button id="prev-btn" class="nav-btn" style="width: 30px; height: 66px; background-color: var(--color-6); border-radius: 8px; border: 1px solid black; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <span class="material-icons-outlined" style="font-size: 24px; color: var(--color-2);">chevron_left</span>
                </button>
                <div id="category-container" style="display: flex; flex-wrap: nowrap; gap: 15px; padding: 10px 0; overflow: hidden; flex: 1;">
                    <!-- Categories will be rendered here via JavaScript -->
                </div>
                <button id="next-btn" class="nav-btn" style="width: 30px; height: 66px; background-color: var(--color-6); border-radius: 8px; border: 1px solid black; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <span class="material-icons-outlined" style="font-size: 24px; color: var(--color-2);">chevron_right</span>
                </button>
            </div>

            <h2 style="margin: 20px 0 10px;">Products</h2>
            <div class="products-container" style="max-height: 700px; overflow-y: auto; display: flex; flex-wrap: wrap; gap: 20px; padding: 10px 0;" id="products-container">
                @foreach ($products as $product)
                    @if ($product->stockQuantity > 0)
                        <button class="product-item" data-product-id="{{ $product->productID }}" data-stock="{{ $product->stockQuantity }}" style="width: calc(33.33% - 14px); height: 150px; background-color: var(--color-6); border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border: 1px solid black; padding: 15px; cursor: pointer;">
                            <div style="display: flex; align-items: center;">
                                <span class="material-icons-outlined product-icon" style="font-size: 60px; color: var(--color-2); margin-right: 10px;">
                                    @switch($product->categoryName)
                                        @case('CPU') memory @break
                                        @case('Storage') storage @break
                                        @case('SSD') sd_card @break
                                        @case('HDD') save @break
                                        @case('Cables') cable @break
                                        @case('Ethernet Cable') lan @break
                                        @case('GPU') videogame_asset @break
                                        @case('Laptop') laptop @break
                                        @case('Laptop Screen') monitor @break
                                        @case('Laptop Battery') battery_full @break
                                        @default category
                                    @endswitch
                                </span>
                                <div>
                                    <p style="margin: 0; font-size: 16px; font-weight: 500;">{{ $product->productName }}</p>
                                    <p style="margin: 5px 0 0; font-size: 12px; color: var(--color-3);">{{ $product->productDescription ?? 'No description available' }}</p>
                                </div>
                            </div>
                            <p style="margin: 0; font-size: 14px; text-align: right;">₱{{ number_format($product->price, 2) }}</p>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Invoice Side (Right, 30%) -->
        <div class="right-section" id="invoice-section">
            <div class="invoice-upper">
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; color: var(--color-1);">Cart</h2>
                    <span id="invoice-count" class="invoice-count" style="margin-left: 10px;">0</span>
                </div>
                <div class="invoice-items-container" id="invoice-items"></div>
            </div>
            <div class="invoice-lower">
                <div class="payment-frame" id="payment-frame">
                    <h4>Payment Summary</h4>
                    <div class="payment-details" id="payment-details">
                        <p>Items Ordered</p>
                        <p id="items-ordered">0</p>
                        <p style="font-weight: bold; font-size: 1.1rem;">Grand Total</p>
                        <p id="grand-total" style="font-weight: bold; font-size: 1.1rem;">₱0.00</p>
                    </div>
                    <div class="payment-methods">
                        <button class="payment-btn cash" data-method="cash">
                            <span class="material-icons-outlined">money</span>
                            Cash
                        </button>
                        <button class="payment-btn gcash" data-method="gcash">
                            <img width="50" height="50" src="https://img.icons8.com/plasticine/50/gcash.png" alt="gcash">
                            GCash
                        </button>
                    </div>
                    <button id="place-order-btn">Place an Order</button>
                </div>
            </div>
        </div>

        <!-- Cart Invoice Button for Mobile/Tablet -->
        <button id="toggle-invoice-btn" class="cart-invoice-btn">
            <span class="material-icons-outlined">shopping_cart</span>
            <span class="cart-count" id="cart-count">0</span>
        </button>
    </div>

    <!-- JavaScript for toggling, filtering, searching, and invoice -->
    <script>
        const brands = @json($brands);
        const categories = @json($categories);
        let products = @json($products);
        const employeeID = @json($employee->employeeID);
        let showingCategories = true;
        let currentFilter = { type: null, id: null };
        let invoice = [];
        let isPaymentFormVisible = false;
        let grandTotal = 0;
        let currentPage = 0;
        let itemsPerPage = window.innerWidth <= 767 ? 2 : window.innerWidth <= 991 ? 4 : 5; // 2 for mobile, 4 for tablet, 5 for desktop
        let searchQuery = '';
        let selectedPaymentMethod = null;

        // Update itemsPerPage on resize
        window.addEventListener('resize', () => {
            itemsPerPage = window.innerWidth <= 767 ? 2 : window.innerWidth <= 991 ? 4 : 5;
            if (showingCategories) {
                renderCategories();
            } else {
                renderBrands();
            }
        });

        function renderCategories() {
            const container = document.getElementById('category-container');
            container.innerHTML = '';
            const start = currentPage * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedCategories = categories.slice(start, end);

            paginatedCategories.forEach(category => {
                container.innerHTML += `
                    <button class="item category-btn" data-category-id="${category.categoryID}" style="width: calc(20% - 12px); height: 66px; background-color: var(--color-6); border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border: 1px solid black; cursor: pointer;">
                        <div style="display: flex; align-items: center;">
                            <span class="material-icons-outlined" style="font-size: 24px; color: var(--color-2); margin-right: 8px;">
                                ${getIcon(category.categoryName)}
                            </span>
                            <p style="margin: 0; font-size: 16px;">${category.categoryName}</p>
                        </div>
                    </button>
                `;
            });

            document.getElementById('section-title').textContent = 'Categories';
            updateNavButtons(categories.length);
            attachCategoryListeners();
        }

        function renderBrands() {
            const container = document.getElementById('category-container');
            container.innerHTML = '';
            const start = currentPage * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedBrands = brands.slice(start, end);

            paginatedBrands.forEach(brand => {
                let brandSvg = '';
                switch (brand.brandName.toLowerCase()) {
                    case 'amd':
                        brandSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="m18.324 9.137l1.559 1.56h2.556v2.557L24 14.814V9.137zM2 9.52l-2 4.96h1.309l.37-.982H3.9l.408.982h1.338L3.432 9.52zm4.209 0v4.955h1.238v-3.092l1.338 1.562h.188l1.338-1.556v3.091h1.238V9.52H10.47l-1.592 1.845L7.287 9.52zm6.283 0v4.96h2.057c1.979 0 2.88-1.046 2.88-2.472c0-1.36-.937-2.488-2.747-2.488zm1.237.91h.792c1.17 0 1.63.711 1.63 1.57c0 .728-.372 1.572-1.616 1.572h-.806zm-10.985.273l.791 1.932H2.008zm17.137.307l-1.604 1.603v2.25h2.246l1.604-1.607h-2.246z"/></svg>`;
                        break;
                    case 'nvidia':
                        brandSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M312.805 18.394v63.838h18.015V18.394zm-141.742-.108v63.892h18.178v-49.61l14.174.054c4.653 0 7.899 1.136 10.117 3.517c2.867 3.03 4.003 7.952 4.003 16.879v29.16h17.637V46.904c0-25.21-16.068-28.618-31.757-28.618zm170.793.108v63.838h29.214c15.58 0 20.666-2.597 26.13-8.386c3.896-4.057 6.384-13.038 6.384-22.83c0-8.98-2.11-16.987-5.843-21.964c-6.6-8.927-16.23-10.658-30.62-10.658zm17.853 13.85h7.736c11.253 0 18.503 5.03 18.503 18.123s-7.25 18.177-18.503 18.177h-7.736zm-72.872-13.85l-15.04 50.583l-14.39-50.583H237.93l20.558 63.838h25.967l20.775-63.838zm125.187 63.838h18.015V18.394h-18.015zm50.529-63.838l-25.157 63.784h17.745l4.004-11.307h29.754l3.787 11.252H512l-25.373-63.73zm11.685 11.631l10.929 29.863h-22.181zM54.803 28.997v-8.764c.866-.054 1.731-.108 2.597-.108c24.02-.757 39.763 20.666 39.763 20.666S80.176 64.38 61.944 64.38c-2.434 0-4.815-.379-7.087-1.136V36.626c9.36 1.136 11.253 5.247 16.825 14.606L84.18 40.737s-9.143-11.956-24.507-11.956c-1.623-.054-3.246.054-4.869.216m0-28.997v13.092l2.597-.162c33.38-1.136 55.182 27.374 55.182 27.374S87.587 70.708 61.566 70.708c-2.273 0-4.49-.216-6.709-.595v8.115c1.84.217 3.733.379 5.572.379c24.237 0 41.765-12.389 58.753-26.996c2.813 2.272 14.336 7.736 16.717 10.117c-16.122 13.525-53.721 24.399-75.037 24.399a53 53 0 0 1-5.95-.325v11.415h92.077V0zm0 63.243v6.924c-22.397-4.003-28.619-27.32-28.619-27.32s10.766-11.902 28.619-13.85v7.574h-.054c-9.36-1.136-16.717 7.628-16.717 7.628s4.166 14.77 16.771 19.044M15.04 41.873s13.254-19.584 39.817-21.64v-7.14C25.427 15.472 0 40.357 0 40.357s14.39 41.657 54.803 45.444v-7.574C25.156 74.55 15.04 41.873 15.04 41.873"/></svg>`;
                        break;
                    case 'intel':
                        brandSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M150.848 230.395v82.68H124.53V209.15l54.337.054c23.078 0 30.94 16.275 30.94 31.058v72.813h-26.271v-72.677c0-6.192-3.206-10.003-10.938-10.003zm157.583 20.934h40.491c.631-30.563-40.534-28.566-40.49 0m66.763 17.913h-66.763c-.796 24.687 30.53 31.48 47.435 13.462l16.256 15.521c-10.409 10.276-21.33 16.516-40.574 16.516c-25.194 0-49.306-13.738-49.306-53.776c0-34.21 21.052-53.576 48.714-53.576c30.121.56 46.618 23.957 44.238 61.853m-115.17 43.703c-21.44 0-30.538-14.926-30.538-29.668V180.835h26.278v28.315h19.81v21.245h-19.81v51.235c0 6.038 2.851 9.389 9.12 9.389h10.69v21.926zM101.112 194.788h-26.5v-25.172h26.5zm.067 119.38c-19.845-1.907-26.595-13.944-26.595-27.834l.029-77.184h26.566zm315.84-2.22c-19.803-1.911-26.521-13.929-26.521-27.8V166.034h26.522zm92.718-128.845C485.701 65.92 258.23 58.501 111.61 147.76v9.854c146.471-75.274 354.203-74.83 373.129 33.101c6.332 35.689-13.779 72.898-49.696 94.286v27.982c43.231-15.816 87.567-67.102 74.694-129.88M243.215 388.13c-101.191 9.354-206.64-5.334-221.394-84.406c-7.222-38.98 10.536-80.3 34.093-105.968v-13.725C13.506 221.22-9.51 268.298 3.74 323.927c16.911 71.333 107.412 111.758 245.546 98.333c54.653-5.334 126.186-22.907 175.917-50.202V333.28c-45.125 26.87-119.797 49.09-181.99 54.849"/></svg>`;
                        break;
                    default:
                        brandSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M23.904 10.788V9.522h-4.656c-.972 0-1.41.6-1.482 1.182v.018v-1.2h-1.368v1.266h1.362zm-6.144.456l-1.368-.078v1.458c0 .456-.228.594-1.02.594H14.28c-.654 0-.93-.186-.93-.594v-1.596l-1.386-.102v1.812h-.03c-.078-.528-.276-1.14-1.596-1.23L6 11.22c0 .666.474 1.062 1.218 1.14l3.024.306c.24.018.414.09.414.288c0 .216-.18.24-.456.24H5.946V11.22l-1.386-.09v3.348h5.646c1.26 0 1.662-.654 1.722-1.2h.03c.156.864.912 1.2 2.19 1.2h1.41c1.494 0 2.202-.456 2.202-1.524zm4.398.258l-4.338-.258c0 .666.438 1.11 1.182 1.17l3.09.24c.24.018.384.078.384.276c0 .186-.168.258-.516.258h-4.212v1.29h4.302c1.356 0 1.95-.474 1.95-1.554c0-.972-.534-1.338-1.842-1.422m-10.194-1.98h1.386v1.266h-1.386zM3.798 11.07l-1.506-.15L0 14.478h1.686zm7.914-1.548h-4.23c-.984 0-1.416.612-1.518 1.2v-1.2H3.618c-.33 0-.486.102-.642.33l-.648.936h9.384Z"/></svg>`;
                }
                container.innerHTML += `
                    <button class="item brand-btn" data-brand-id="${brand.brandID}" style="width: calc(20% - 12px); height: 66px; background-color: var(--color-6); border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border: 1px solid black; cursor: pointer; position: relative; overflow: hidden;">
                        ${brandSvg}
                    </button>
                `;
            });

            document.getElementById('section-title').textContent = 'Brands';
            updateNavButtons(brands.length);
            attachBrandListeners();
        }

        function updateNavButtons(totalItems) {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const maxPage = Math.ceil(totalItems / itemsPerPage) - 1;

            prevBtn.disabled = currentPage === 0;
            nextBtn.disabled = currentPage >= maxPage;

            prevBtn.style.opacity = prevBtn.disabled ? '0.5' : '1';
            nextBtn.style.opacity = nextBtn.disabled ? '0.5' : '1';
            prevBtn.style.cursor = prevBtn.disabled ? 'not-allowed' : 'pointer';
            nextBtn.style.cursor = nextBtn.disabled ? 'not-allowed' : 'pointer';
        }

        function renderProducts(filteredProducts) {
            const container = document.getElementById('products-container');
            container.innerHTML = '';
            filteredProducts.forEach(product => {
                if (product.stockQuantity > 0) {
                    container.innerHTML += `
                        <button class="product-item" data-product-id="${product.productID}" data-stock="${product.stockQuantity}" style="width: calc(33.33% - 14px); height: 150px; background-color: var(--color-6); border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border: 1px solid black; padding: 15px; cursor: pointer;">
                            <div style="display: flex; align-items: center;">
                                <span class="material-icons-outlined product-icon" style="font-size: 60px; color: var(--color-2); margin-right: 10px;">
                                    ${getIcon(product.categoryName)}
                                </span>
                                <div>
                                    <p style="margin: 0; font-size: 16px; font-weight: 500;">${product.productName}</p>
                                    <p style="margin: 5px 0 0; font-size: 12px; color: var(--color-3);">${product.productDescription || 'No description available'}</p>
                                </div>
                            </div>
                            <p style="margin: 0; font-size: 14px; text-align: right;">₱${Number(product.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</p>
                        </button>
                    `;
                }
            });
            attachProductListeners();
        }

        function getIcon(categoryName) {
            switch (categoryName) {
                case 'CPU': return 'memory';
                case 'Storage': return 'storage';
                case 'SSD': return 'sd_card';
                case 'HDD': return 'save';
                case 'Cables': return 'cable';
                case 'Ethernet Cable': return 'lan';
                case 'GPU': return 'videogame_asset';
                case 'Laptop': return 'laptop';
                case 'Laptop Screen': return 'monitor';
                case 'Laptop Battery': return 'battery_full';
                default: return 'category';
            }
        }

        function filterProducts() {
            let filteredProducts = products;

            if (searchQuery) {
                filteredProducts = filteredProducts.filter(product =>
                    product.productName.toLowerCase().includes(searchQuery.toLowerCase())
                );
            }

            if (currentFilter.type === 'category' && currentFilter.id) {
                filteredProducts = filteredProducts.filter(product => product.categoryID === currentFilter.id);
            } else if (currentFilter.type === 'brand' && currentFilter.id) {
                filteredProducts = filteredProducts.filter(product => product.brandID === currentFilter.id);
            }

            renderProducts(filteredProducts);
        }

        function attachCategoryListeners() {
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const categoryId = parseInt(btn.getAttribute('data-category-id'));
                    currentFilter = { type: 'category', id: categoryId };
                    filterProducts();
                });
            });
        }

        function attachBrandListeners() {
            document.querySelectorAll('.brand-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const brandId = parseInt(btn.getAttribute('data-brand-id'));
                    currentFilter = { type: 'brand', id: brandId };
                    filterProducts();
                });
            });
        }

        function attachProductListeners() {
            document.querySelectorAll('.product-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = parseInt(btn.getAttribute('data-product-id'));
                    const stock = parseInt(btn.getAttribute('data-stock'));
                    const product = products.find(p => p.productID === productId);
                    if (stock <= 0) {
                        alert(`Product ${product.productName} is out of stock!`);
                        return;
                    }
                    addToInvoice(product);
                });
            });
        }

        function addToInvoice(product) {
            const existingItem = invoice.find(item => item.productID === product.productID);
            if (existingItem) {
                if (existingItem.quantity + 1 > product.stockQuantity) {
                    alert(`Cannot add more of ${product.productName}. Only ${product.stockQuantity} in stock.`);
                    return;
                }
                existingItem.quantity += 1;
            } else {
                if (product.stockQuantity < 1) {
                    alert(`Cannot add ${product.productName}. Out of stock.`);
                    return;
                }
                invoice.push({ ...product, quantity: 1 });
            }
            renderInvoice();
        }

        function removeFromInvoice(productId) {
            invoice = invoice.filter(item => item.productID !== productId);
            renderInvoice();
        }

        function updateQuantity(productId, change) {
            const item = invoice.find(item => item.productID === productId);
            const product = products.find(p => p.productID === productId);
            if (item) {
                const newQuantity = item.quantity + change;
                if (newQuantity < 1) {
                    removeFromInvoice(productId);
                } else if (newQuantity > product.stockQuantity) {
                    alert(`Cannot set quantity to ${newQuantity} for ${product.productName}. Only ${product.stockQuantity} in stock.`);
                } else {
                    item.quantity = newQuantity;
                    renderInvoice();
                }
            }
        }

        function renderInvoice() {
            const invoiceContainer = document.getElementById('invoice-items');
            invoiceContainer.innerHTML = '';
            invoice.forEach(item => {
                const totalPrice = (item.price * item.quantity).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                invoiceContainer.innerHTML += `
                    <div style="position: relative; background-color: var(--color-6); border-radius: 8px; padding: 12px; margin-bottom: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <button class="delete-btn" data-product-id="${item.productID}" style="position: absolute; top: 4px; right: 4px; background: none; border: none; cursor: pointer;">
                            <span class="material-icons-outlined" style="font-size: 20px; color: var(--color-2);">close</span>
                        </button>
                        <div style="display: flex; align-items: center;">
                            <span class="material-icons-outlined" style="font-size: 48px; color: var(--color-2); margin-right: 8px;">${getIcon(item.categoryName)}</span>
                            <p style="margin: 0; font-size: 14px; font-weight: 500; flex-grow: 1; color: var(--color-2);">${item.productName}</p>
                            <p style="margin: 0; font-size: 12px; color: var(--color-2);">₱${Number(item.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</p>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                            <div style="display: flex; align-items: center;">
                                <button class="quantity-btn minus" data-product-id="${item.productID}" style="width: 24px; height: 24px; background-color: var(--color-2); color: var(--color-1); border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">-</button>
                                <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-product-id="${item.productID}" style="width: 40px; height: 24px; text-align: center; border: 1px solid var(--color-3); border-radius: 4px; margin: 0 4px; font-size: 12px; color: var(--color-1); background-color: var(--color-2); -webkit-appearance: none; -moz-appearance: textfield;">
                                <button class="quantity-btn plus" data-product-id="${item.productID}" style="width: 24px; height: 24px; background-color: var(--color-2); color: var(--color-1); border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">+</button>
                            </div>
                            <p style="margin: 0; font-size: 14px; font-weight: bold; color: var(--color-2);">₱${totalPrice}</p>
                        </div>
                    </div>
                `;
            });

            grandTotal = invoice.reduce((sum, item) => sum + item.price * item.quantity, 0);
            const itemsOrdered = invoice.reduce((sum, item) => sum + item.quantity, 0);

            const grandTotalEl = document.getElementById('grand-total');
            const itemsOrderedEl = document.getElementById('items-ordered');
            const invoiceCountEl = document.getElementById('invoice-count');
            const cartCountEl = document.getElementById('cart-count');
            if (grandTotalEl) grandTotalEl.textContent = `₱${grandTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
            if (itemsOrderedEl) itemsOrderedEl.textContent = itemsOrdered;
            if (invoiceCountEl) invoiceCountEl.textContent = invoice.length;
            if (cartCountEl) cartCountEl.textContent = itemsOrdered;

            if (!isPaymentFormVisible) {
                renderPaymentSummary();
            }

            attachInvoiceListeners();
        }

        function renderPaymentSummary() {
            const itemsOrdered = invoice.reduce((sum, item) => sum + item.quantity, 0);
            const paymentFrame = document.getElementById('payment-frame');
            paymentFrame.innerHTML = `
                <h4>Payment Summary</h4>
                <div class="payment-details" id="payment-details">
                    <p>Items Ordered</p>
                    <p id="items-ordered">${itemsOrdered}</p>
                    <p style="font-weight: bold; font-size: 1.1rem;">Grand Total</p>
                    <p id="grand-total" style="font-weight: bold; font-size: 1.1rem;">₱${grandTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</p>
                </div>
                <div class="payment-methods">
                    <button class="payment-btn cash ${selectedPaymentMethod === 'cash' ? 'selected' : ''}" data-method="cash">
                        <span class="material-icons-outlined">money</span>
                        Cash
                    </button>
                    <button class="payment-btn gcash ${selectedPaymentMethod === 'gcash' ? 'selected' : ''}" data-method="gcash">
                        <img width="50" height="50" src="https://img.icons8.com/plasticine/50/gcash.png" alt="gcash">
                        GCash
                    </button>
                </div>
                <button id="place-order-btn">Place an Order</button>
            `;
            attachPaymentMethodListeners(); // Re-attach listeners after rendering
            document.getElementById('place-order-btn').addEventListener('click', showPaymentForm);
        }

        function attachPaymentMethodListeners() {
            const buttons = document.querySelectorAll('.payment-btn');
            console.log('Found payment buttons:', buttons.length); // Debug log
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    console.log('Button clicked:', btn.getAttribute('data-method'));
                    selectedPaymentMethod = btn.getAttribute('data-method');
                    document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    renderPaymentSummary();
                });
            });
        }

        function showPaymentForm() {
            if (invoice.length === 0) {
                alert('Please add items to the invoice before placing an order.');
                return;
            }
            if (!selectedPaymentMethod) {
                alert('Please select a payment method (Cash or GCash).');
                return;
            }

            isPaymentFormVisible = true;
            document.querySelector('.invoice-upper').style.display = 'none';
            document.querySelector('.payment-frame').classList.add('expanded');
            document.querySelector('.invoice-lower').style.height = '100%';

            const paymentFrame = document.getElementById('payment-frame');
            paymentFrame.innerHTML = `
                <div class="payment-form-header">
                    <button id="back-btn" class="back-btn">
                        <span class="material-icons-outlined">arrow_back</span>
                    </button>
                    <h4>Payment Summary</h4>
                </div>
                <div class="payment-form">
                    <div class="row mb-1 align-items-center">
                        <label for="customer-name" class="col-5 text-nowrap" style="color: var(--color-2);">Customer Name:</label>
                        <div class="col-7">
                            <input type="text" id="customer-name" placeholder="Enter customer name" class="form-control" style="border: 1px solid var(--color-3); border-radius: 4px; background-color: var(--color-2); color: var(--color-1);">
                        </div>
                    </div>
                    ${selectedPaymentMethod === 'gcash' ? `
                        <div class="row mb-1 align-items-center">
                            <label for="gcash-number" class="col-5 text-nowrap" style="color: var(--color-2);">GCash Number:</label>
                            <div class="col-7">
                                <input type="text" id="gcash-number" placeholder="09XXXXXXXXX" class="form-control" style="border: 1px solid var(--color-3); border-radius: 4px; background-color: var(--color-2); color: var(--color-1);">
                            </div>
                        </div>
                        <div class="row mb-1 align-items-center">
                            <label for="reference-number" class="col-5 text-nowrap" style="color: var(--color-2);">Reference Number:</label>
                            <div class="col-7">
                                <input type="number" id="reference-number" placeholder="Enter GCash reference number" class="form-control" style="border: 1px solid var(--color-3); border-radius: 4px; background-color: var(--color-2); color: var(--color-1);" min="10" max="99999999999999999999" required>
                            </div>
                        </div>
                    ` : ''}
                    <div class="row mb-1 align-items-center">
                        <label for="amount-received" class="col-5 text-nowrap" style="color: var(--color-2);">Amount Received:</label>
                        <div class="col-7">
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--color-2); color: var(--color-3); border: 1px solid var(--color-3); border-right: none;">₱</span>
                                <input type="number" id="amount-received" placeholder="0.00" step="0.01" min="0" class="form-control" style="border: 1px solid var(--color-3); border-radius: 0 4px 4px 0; background-color: var(--color-2); color: var(--color-1);">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1 align-items-center">
                        <label for="change" class="col-5 text-nowrap" style="color: var(--color-2);">Change:</label>
                        <div class="col-7">
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--color-2); color: var(--color-3); border: 1px solid var(--color-3); border-right: none;">₱</span>
                                <input type="number" id="change" readonly class="form-control" style="border: 1px solid var(--color-3); border-radius: 0 4px 4px 0; background-color: var(--color-2); color: var(--color-1);">
                            </div>
                        </div>
                    </div>
                    <div class="receipt-frame" id="receipt-frame"></div>
                </div>
                <button id="confirm-order-btn" class="btn btn-primary w-100">Confirm Order</button>
            `;

            const customerNameInput = document.getElementById('customer-name');
            const gcashNumberInput = document.getElementById('gcash-number');
            const referenceNumberInput = document.getElementById('reference-number');
            const amountReceivedInput = document.getElementById('amount-received');
            const changeInput = document.getElementById('change');
            const confirmOrderBtn = document.getElementById('confirm-order-btn');
            const backBtn = document.getElementById('back-btn');

            function updateReceipt() {
                const customerName = customerNameInput.value.trim() || 'N/A';
                const gcashNumber = gcashNumberInput ? gcashNumberInput.value.trim() || 'N/A' : 'N/A';
                const referenceNumber = referenceNumberInput ? referenceNumberInput.value.trim() || 'N/A' : 'N/A';
                const amountReceived = parseFloat(amountReceivedInput.value) || 0;
                const change = amountReceived - grandTotal >= 0 ? (amountReceived - grandTotal).toFixed(2) : '0.00';
                const itemsOrdered = invoice.reduce((sum, item) => sum + item.quantity, 0);

                const receiptFrame = document.getElementById('receipt-frame');
                receiptFrame.innerHTML = `
                    <div class="receipt-content">
                        <h5>Receipt Preview</h5>
                        <div class="receipt-line"></div>
                        <p>Customer: ${customerName}</p>
                        ${selectedPaymentMethod === 'gcash' ? `
                            <p>GCash Number: ${gcashNumber}</p>
                            <p>Reference Number: ${referenceNumber}</p>
                        ` : ''}
                        <div class="receipt-line"></div>
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoice.map(item => `
                                    <tr>
                                        <td>${item.productName}</td>
                                        <td>${item.quantity}</td>
                                        <td>₱${Number(item.price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                                        <td>₱${(item.price * item.quantity).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        <div class="receipt-line"></div>
                        <p>Payment Method: ${selectedPaymentMethod.charAt(0).toUpperCase() + selectedPaymentMethod.slice(1)}</p>
                        <p>Items Ordered: ${itemsOrdered}</p>
                        <p>Amount Received: ₱${amountReceived.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</p>
                        <p>Change: ₱${change.replace(/\d(?=(\d{3})+\.)/g, '$&,')}</p>
                        <div class="receipt-line"></div>
                        <p style="font-weight: bold; font-size: 1.1rem;">Grand Total: ₱${grandTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</p>
                    </div>
                `;
            }

            customerNameInput.addEventListener('input', updateReceipt);
            if (gcashNumberInput) {
                gcashNumberInput.addEventListener('input', updateReceipt);
            }
            if (referenceNumberInput) {
                referenceNumberInput.addEventListener('input', updateReceipt);
            }
            amountReceivedInput.addEventListener('input', () => {
                const amountReceived = parseFloat(amountReceivedInput.value) || 0;
                const change = amountReceived - grandTotal;
                changeInput.value = change >= 0 ? change.toFixed(2) : '0.00';
                updateReceipt();
            });

            confirmOrderBtn.addEventListener('click', confirmOrder);
            backBtn.addEventListener('click', () => {
                isPaymentFormVisible = false;
                document.querySelector('.invoice-upper').style.display = 'flex';
                document.querySelector('.payment-frame').classList.remove('expanded');
                document.querySelector('.invoice-lower').style.height = 'auto';
                renderPaymentSummary();
            });

            updateReceipt();
        }

        function confirmOrder() {
            const customerName = document.getElementById('customer-name').value.trim();
            const gcashNumber = document.getElementById('gcash-number') ? document.getElementById('gcash-number').value.trim() : null;
            const referenceNumber = document.getElementById('reference-number') ? document.getElementById('reference-number').value.trim() : null;
            const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;

            if (!customerName) {
                alert('Please enter a customer name.');
                return;
            }
            if (selectedPaymentMethod === 'gcash' && (!gcashNumber || !/^(09)[0-9]{9}$/.test(gcashNumber))) {
                alert('Please enter a valid 11-digit GCash number starting with 09.');
                return;
            }
            if (selectedPaymentMethod === 'gcash' && (!referenceNumber || !/^[0-9]{2,20}$/.test(referenceNumber))) {
                alert('Please enter a valid GCash reference number (2-20 digits).');
                return;
            }
            if (amountReceived < grandTotal) {
                alert('Amount received must be at least the grand total.');
                return;
            }

            for (const item of invoice) {
                const product = products.find(p => p.productID === item.productID);
                if (item.quantity > product.stockQuantity) {
                    alert(`Cannot place order. ${product.productName} has only ${product.stockQuantity} in stock.`);
                    return;
                }
            }

            const orderData = {
                customer_name: customerName,
                amount_received: amountReceived,
                payment_status: selectedPaymentMethod,
                gcash_number: gcashNumber,
                reference_number: referenceNumber,
                items: invoice.map(item => ({
                    productID: item.productID,
                    quantity: item.quantity,
                    price: item.price,
                })),
                grand_total: grandTotal,
                _token: '{{ csrf_token() }}',
            };

            console.log('Order Data:', orderData); // Debug log

            fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': orderData._token,
                },
                body: JSON.stringify(orderData),
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || `HTTP error! Status: ${response.status}`); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(`Order confirmed for ${customerName} via ${selectedPaymentMethod}! Change: ₱${(amountReceived - grandTotal).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')} (Order ID: ${data.order_id}, Reference: ${data.reference_number})`);
                    invoice = [];
                    grandTotal = 0;
                    isPaymentFormVisible = false;
                    selectedPaymentMethod = null;
                    document.getElementById('invoice-items').innerHTML = '';
                    document.getElementById('invoice-count').textContent = '0';
                    document.getElementById('cart-count').textContent = '0';
                    document.querySelector('.invoice-upper').style.display = 'flex';
                    document.querySelector('.payment-frame').classList.remove('expanded');
                    document.querySelector('.invoice-lower').style.height = 'auto';
                    renderPaymentSummary();
                    // Hide invoice section on mobile/tablet after order
                    if (window.innerWidth <= 991) {
                        document.getElementById('invoice-section').classList.remove('show');
                    }
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(`Failed to place order: ${error.message}`);
            });
        }

        function attachInvoiceListeners() {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = parseInt(btn.getAttribute('data-product-id'));
                    removeFromInvoice(productId);
                });
            });
            document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = parseInt(btn.getAttribute('data-product-id'));
                    updateQuantity(productId, -1);
                });
            });
            document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = parseInt(btn.getAttribute('data-product-id'));
                    updateQuantity(productId, 1);
                });
            });
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', () => {
                    const productId = parseInt(input.getAttribute('data-product-id'));
                    const newQuantity = parseInt(input.value) || 1;
                    const product = products.find(p => p.productID === productId);
                    if (newQuantity > product.stockQuantity) {
                        alert(`Cannot set quantity to ${newQuantity} for ${product.productName}. Only ${product.stockQuantity} in stock.`);
                        input.value = product.stockQuantity;
                    } else {
                        const item = invoice.find(item => item.productID === productId);
                        if (item) {
                            item.quantity = Math.max(1, newQuantity);
                            renderInvoice();
                        }
                    }
                });
            });
        }

        document.getElementById('switch-btn').addEventListener('click', () => {
            showingCategories = !showingCategories;
            currentPage = 0;
            currentFilter = { type: null, id: null };
            if (showingCategories) {
                renderCategories();
                filterProducts();
            } else {
                renderBrands();
                filterProducts();
            }
        });

        document.getElementById('prev-btn').addEventListener('click', () => {
            if (currentPage > 0) {
                currentPage--;
                if (showingCategories) {
                    renderCategories();
                } else {
                    renderBrands();
                }
            }
        });

        document.getElementById('next-btn').addEventListener('click', () => {
            const totalItems = showingCategories ? categories.length : brands.length;
            const maxPage = Math.ceil(totalItems / itemsPerPage) - 1;
            if (currentPage < maxPage) {
                currentPage++;
                if (showingCategories) {
                    renderCategories();
                } else {
                    renderBrands();
                }
            }
        });

        document.querySelector('.search-input')?.addEventListener('input', (e) => {
            searchQuery = e.target.value.trim();
            filterProducts();
        });

        // Toggle invoice section for mobile/tablet
        document.getElementById('toggle-invoice-btn').addEventListener('click', () => {
            const invoiceSection = document.getElementById('invoice-section');
            invoiceSection.classList.toggle('show');
        });

        renderCategories();
        filterProducts();

        console.log('User authenticated:', {{ Auth::check() ? 'true' : 'false' }});
    </script>
@endsection