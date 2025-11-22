{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.before') !!}

<v-payment-methods
    :methods="paymentMethods"
    @processing="stepForward"
    @processed="stepProcessed"
>
    <x-shop::shimmer.checkout.onepage.payment-method />
</v-payment-methods>

{!! view_render_event('bagisto.shop.checkout.onepage.payment_methods.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-payment-methods-template"
    >
        <div class="mb-7 max-md:last:!mb-0">
            <template v-if="! ready">
                <!-- Payment Method shimmer Effect -->
                <x-shop::shimmer.checkout.onepage.payment-method />
            </template>

            <template v-else>
                {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.before') !!}

                <!-- Accordion Blade Component -->
                <x-shop::accordion class="overflow-hidden !border-b-0 max-md:rounded-lg max-md:!border-none max-md:!bg-gray-100">
                    <!-- Accordion Blade Component Header -->
                    <x-slot:header class="px-0 py-4 max-md:p-3 max-md:text-sm max-md:font-medium max-sm:p-2">

                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-medium max-md:text-base">
                                @lang('shop::app.checkout.onepage.payment.payment-method')
                            </h2>
                        </div>
                    </x-slot>

                    <!-- Accordion Blade Component Content -->
                    <x-slot:content class="mt-8 !p-0 max-md:mt-0 max-md:rounded-t-none max-md:border max-md:border-t-0 max-md:!p-4">
                        <div class="flex flex-wrap gap-7 max-md:gap-4 max-sm:gap-2.5">
                            <div
                                class="relative cursor-pointer max-md:max-w-full max-md:flex-auto"
                                v-for="(payment, index) in list"
                            >
                                {!! view_render_event('bagisto.shop.checkout.payment-method.before') !!}

                                <input
                                    type="radio"
                                    name="payment[method]"
                                    :value="payment.method"
                                    :id="payment.method"
                                    class="peer hidden"
                                    :checked="selectedPaymentMethod === payment.method"
                                    @change="store(payment)"
                                >

                                <label
                                    :for="payment.method"
                                    class="icon-radio-unselect peer-checked:icon-radio-select absolute top-5 cursor-pointer text-2xl text-navyBlue ltr:right-5 rtl:left-5"
                                >
                                </label>

                                <label
                                    :for="payment.method"
                                    class="block w-[190px] cursor-pointer rounded-xl border border-zinc-200 p-5 max-md:flex max-md:w-full max-md:gap-5 max-md:rounded-lg max-sm:gap-4 max-sm:px-4 max-sm:py-2.5"
                                >
                                    {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.image.before') !!}

                                    <img
                                        class="max-h-11 max-w-14"
                                        :src="payment.image"
                                        width="55"
                                        height="55"
                                        :alt="payment.method_title"
                                        :title="payment.method_title"
                                    />

                                    {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.image.after') !!}

                                    <div>
                                        {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.title.before') !!}

                                        <p class="mt-1.5 text-sm font-semibold max-md:mt-1 max-sm:mt-0">
                                            @{{ payment.method_title }}
                                        </p>

                                        {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.title.after') !!}

                                        {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.description.before') !!}

                                        <p class="mt-2.5 text-xs font-medium text-zinc-500 max-md:mt-1 max-sm:mt-0">
                                            @{{ payment.description }}
                                        </p>

                                        {!! view_render_event('bagisto.shop.checkout.onepage.payment-method.description.after') !!}

                                    </div>
                                </label>

                                {!! view_render_event('bagisto.shop.checkout.payment-method.after') !!}
                            </div>
                        </div>
                    </x-slot>
                </x-shop::accordion>

                {!! view_render_event('bagisto.shop.checkout.onepage.payment_method.accordion.after') !!}
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-payment-methods', {
            template: '#v-payment-methods-template',

            props: {
                methods: {
                    type: [Array, Object],
                    required: true,
                    default: () => null,
                },
            },

            emits: ['processing', 'processed'],

            data() {
                return {
                    selectedPaymentMethod: null,
                };
            },

            computed: {
                list() {
                    if (Array.isArray(this.methods)) {
                        return this.methods;
                    }

                    if (this.methods && Array.isArray(this.methods.payment_methods)) {
                        return this.methods.payment_methods;
                    }

                    return [];
                },

                ready() {
                    return Array.isArray(this.list) && this.list.length >= 0;
                },
            },

            methods: {
                store(selectedMethod) {
                    this.selectedPaymentMethod = selectedMethod.method;
                    this.$emit('processing', 'review');

                    this.$axios.post("{{ route('shop.checkout.onepage.payment_methods.store') }}", {
                            payment: selectedMethod
                        })
                        .then(response => {
                            this.$emit('processed', response.data.cart);

                            // Used in mobile view.
                            if (window.innerWidth <= 768) {
                                window.scrollTo({
                                    top: document.body.scrollHeight,
                                    behavior: 'smooth'
                                });
                            }
                        })
                        .catch(error => {
                            this.$emit('processing', 'payment');

                            if (error.response.data.redirect_url) {
                                window.location.href = error.response.data.redirect_url;
                            }
                        });
                },

                autoSelectFirstPaymentMethod() {
                    // Auto-select first payment method if available and none is selected yet
                    if (!this.list || this.list.length === 0) {
                        return;
                    }

                    // Use nextTick to ensure DOM and data are ready
                    this.$nextTick(() => {
                        // Check if a payment method is already selected in the cart
                        const cartHasPayment = this.methods?.cart?.payment?.method;

                        if (cartHasPayment) {
                            // Set the selected payment method from cart
                            this.selectedPaymentMethod = cartHasPayment;
                        } else {
                            // Auto-select the first available payment method (preferably Stripe)
                            const stripeMethod = this.list.find(m => m.method === 'stripe');
                            const firstMethod = stripeMethod || this.list[0];
                            
                            this.selectedPaymentMethod = firstMethod.method;
                            console.log('Auto-selecting payment method:', firstMethod);
                            this.store(firstMethod);
                        }
                    });
                },
            },
            mounted() {
                this.autoSelectFirstPaymentMethod();
            },
            watch: {
                methods: {
                    handler() {
                        this.autoSelectFirstPaymentMethod();
                    },
                    deep: true,
                },
            }
        });
    </script>
@endPushOnce
