/*!

 Flowplayer Unlimited v6.0.5 (2016-01-13) | flowplayer.org/license

 */
! function(e) {
    function t(e, t, n, r) {
        for (var i, a = n.slice(), l = o(t, e), s = 0, u = a.length; u > s && (handler = a[s], "object" == typeof handler && "function" == typeof handler.handleEvent ? handler.handleEvent(l) : handler.call(e, l), !l.stoppedImmediatePropagation); s++);
        return i = !l.stoppedPropagation, r && i && e.parentNode ? e.parentNode.dispatchEvent(l) : !l.defaultPrevented
    }

    function n(e, t) {
        return {
            configurable: !0,
            get: e,
            set: t
        }
    }

    function r(e, t, r) {
        var o = y(t || e, r);
        g(e, "textContent", n(function() {
            return o.get.call(this)
        }, function(e) {
            o.set.call(this, e)
        }))
    }

    function o(e, t) {
        return e.currentTarget = t, e.eventPhase = e.target === e.currentTarget ? 2 : 3, e
    }

    function i(e, t) {
        for (var n = e.length; n-- && e[n] !== t;);
        return n
    }

    function a() {
        if ("BR" === this.tagName) return "\n";
        for (var e = this.firstChild, t = []; e;) 8 !== e.nodeType && 7 !== e.nodeType && t.push(e.textContent), e = e.nextSibling;
        return t.join("")
    }

    function l(e) {
        !f && k.test(document.readyState) && (f = !f, document.detachEvent(d, l), e = document.createEvent("Event"), e.initEvent(p, !0, !0), document.dispatchEvent(e))
    }

    function s(e) {
        for (var t; t = this.lastChild;) this.removeChild(t);
        null != e && this.appendChild(document.createTextNode(e))
    }

    function u(t, n) {
        return n || (n = e.event), n.target || (n.target = n.srcElement || n.fromElement || document), n.timeStamp || (n.timeStamp = (new Date).getTime()), n
    }
    if (!document.createEvent) {
        var c = !0,
            f = !1,
            d = "onreadystatechange",
            p = "DOMContentLoaded",
            m = "__IE8__" + Math.random(),
            v = e.Object,
            g = v.defineProperty || function(e, t, n) {
                e[t] = n.value
            },
            h = v.defineProperties || function(t, n) {
                for (var r in n)
                    if (b.call(n, r)) try {
                        g(t, r, n[r])
                    } catch (o) {
                        e.console && console.log(r + " failed on object:", t, o.message)
                    }
            },
            y = v.getOwnPropertyDescriptor,
            b = v.prototype.hasOwnProperty,
            w = e.Element.prototype,
            x = e.Text.prototype,
            E = /^[a-z]+$/,
            k = /loaded|complete/,
            T = {},
            S = document.createElement("div");
        r(e.HTMLCommentElement.prototype, w, "nodeValue"), r(e.HTMLScriptElement.prototype, null, "text"), r(x, null, "nodeValue"), r(e.HTMLTitleElement.prototype, null, "text"), g(e.HTMLStyleElement.prototype, "textContent", function(e) {
            return n(function() {
                return e.get.call(this.styleSheet)
            }, function(t) {
                e.set.call(this.styleSheet, t)
            })
        }(y(e.CSSStyleSheet.prototype, "cssText"))), h(w, {
            textContent: {
                get: a,
                set: s
            },
            firstElementChild: {
                get: function() {
                    for (var e = this.childNodes || [], t = 0, n = e.length; n > t; t++)
                        if (1 == e[t].nodeType) return e[t]
                }
            },
            lastElementChild: {
                get: function() {
                    for (var e = this.childNodes || [], t = e.length; t--;)
                        if (1 == e[t].nodeType) return e[t]
                }
            },
            previousElementSibling: {
                get: function() {
                    for (var e = this.previousSibling; e && 1 != e.nodeType;) e = e.previousSibling;
                    return e
                }
            },
            nextElementSibling: {
                get: function() {
                    for (var e = this.nextSibling; e && 1 != e.nodeType;) e = e.nextSibling;
                    return e
                }
            },
            childElementCount: {
                get: function() {
                    for (var e = 0, t = this.childNodes || [], n = t.length; n--; e += 1 == t[n].nodeType);
                    return e
                }
            },
            addEventListener: {
                value: function(e, n, r) {
                    var o, a = this,
                        l = "on" + e,
                        s = a[m] || g(a, m, {
                            value: {}
                        })[m],
                        c = s[l] || (s[l] = {}),
                        f = c.h || (c.h = []);
                    if (!b.call(c, "w")) {
                        if (c.w = function(e) {
                                return e[m] || t(a, u(a, e), f, !1)
                            }, !b.call(T, l))
                            if (E.test(e)) try {
                                o = document.createEventObject(), o[m] = !0, 9 != a.nodeType && null == a.parentNode && S.appendChild(a), a.fireEvent(l, o), T[l] = !0
                            } catch (o) {
                                for (T[l] = !1; S.hasChildNodes();) S.removeChild(S.firstChild)
                            } else T[l] = !1;
                        (c.n = T[l]) && a.attachEvent(l, c.w)
                    }
                    i(f, n) < 0 && f[r ? "unshift" : "push"](n)
                }
            },
            dispatchEvent: {
                value: function(e) {
                    var n, r = this,
                        o = "on" + e.type,
                        i = r[m],
                        a = i && i[o],
                        l = !!a;
                    return e.target || (e.target = r), l ? a.n ? r.fireEvent(o, e) : t(r, e, a.h, !0) : (n = r.parentNode) ? n.dispatchEvent(e) : !0, !e.defaultPrevented
                }
            },
            removeEventListener: {
                value: function(e, t, n) {
                    var r = this,
                        o = "on" + e,
                        a = r[m],
                        l = a && a[o],
                        s = l && l.h,
                        u = s ? i(s, t) : -1;
                    u > -1 && s.splice(u, 1)
                }
            }
        }), h(x, {
            addEventListener: {
                value: w.addEventListener
            },
            dispatchEvent: {
                value: w.dispatchEvent
            },
            removeEventListener: {
                value: w.removeEventListener
            }
        }), h(e.XMLHttpRequest.prototype, {
            addEventListener: {
                value: function(e, t, n) {
                    var r = this,
                        o = "on" + e,
                        a = r[m] || g(r, m, {
                            value: {}
                        })[m],
                        l = a[o] || (a[o] = {}),
                        s = l.h || (l.h = []);
                    i(s, t) < 0 && (r[o] || (r[o] = function() {
                        var t = document.createEvent("Event");
                        t.initEvent(e, !0, !0), r.dispatchEvent(t)
                    }), s[n ? "unshift" : "push"](t))
                }
            },
            dispatchEvent: {
                value: function(e) {
                    var n = this,
                        r = "on" + e.type,
                        o = n[m],
                        i = o && o[r],
                        a = !!i;
                    return a && (i.n ? n.fireEvent(r, e) : t(n, e, i.h, !0))
                }
            },
            removeEventListener: {
                value: w.removeEventListener
            }
        }), h(e.Event.prototype, {
            bubbles: {
                value: !0,
                writable: !0
            },
            cancelable: {
                value: !0,
                writable: !0
            },
            preventDefault: {
                value: function() {
                    this.cancelable && (this.defaultPrevented = !0, this.returnValue = !1)
                }
            },
            stopPropagation: {
                value: function() {
                    this.stoppedPropagation = !0, this.cancelBubble = !0
                }
            },
            stopImmediatePropagation: {
                value: function() {
                    this.stoppedImmediatePropagation = !0, this.stopPropagation()
                }
            },
            initEvent: {
                value: function(e, t, n) {
                    this.type = e, this.bubbles = !!t, this.cancelable = !!n, this.bubbles || this.stopPropagation()
                }
            }
        }), h(e.HTMLDocument.prototype, {
            textContent: {
                get: function() {
                    return 11 === this.nodeType ? a.call(this) : null
                },
                set: function(e) {
                    11 === this.nodeType && s.call(this, e)
                }
            },
            addEventListener: {
                value: function(t, n, r) {
                    var o = this;
                    w.addEventListener.call(o, t, n, r), c && t === p && !k.test(o.readyState) && (c = !1, o.attachEvent(d, l), e == top && function i(e) {
                        try {
                            o.documentElement.doScroll("left"), l()
                        } catch (t) {
                            setTimeout(i, 50)
                        }
                    }())
                }
            },
            dispatchEvent: {
                value: w.dispatchEvent
            },
            removeEventListener: {
                value: w.removeEventListener
            },
            createEvent: {
                value: function(e) {
                    var t;
                    if ("Event" !== e) throw new Error("unsupported " + e);
                    return t = document.createEventObject(), t.timeStamp = (new Date).getTime(), t
                }
            }
        }), h(e.Window.prototype, {
            getComputedStyle: {
                value: function() {
                    function e(e) {
                        this._ = e
                    }

                    function t() {}
                    var n = /^(?:[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|))(?!px)[a-z%]+$/,
                        r = /^(top|right|bottom|left)$/,
                        o = /\-([a-z])/g,
                        i = function(e, t) {
                            return t.toUpperCase()
                        };
                    return e.prototype.getPropertyValue = function(e) {
                            var t, a, l, s = this._,
                                u = s.style,
                                c = s.currentStyle,
                                f = s.runtimeStyle;
                            return e = ("float" === e ? "style-float" : e).replace(o, i), t = c ? c[e] : u[e], n.test(t) && !r.test(e) && (a = u.left, l = f && f.left, l && (f.left = c.left), u.left = "fontSize" === e ? "1em" : t, t = u.pixelLeft + "px", u.left = a, l && (f.left = l)), null == t ? t : t + "" || "auto"
                        }, t.prototype.getPropertyValue = function() {
                            return null
                        },
                        function(n, r) {
                            return r ? new t(n) : new e(n)
                        }
                }()
            },
            addEventListener: {
                value: function(n, r, o) {
                    var a, l = e,
                        s = "on" + n;
                    l[s] || (l[s] = function(e) {
                        return t(l, u(l, e), a, !1)
                    }), a = l[s][m] || (l[s][m] = []), i(a, r) < 0 && a[o ? "unshift" : "push"](r)
                }
            },
            dispatchEvent: {
                value: function(t) {
                    var n = e["on" + t.type];
                    return n ? n.call(e, t) !== !1 && !t.defaultPrevented : !0
                }
            },
            removeEventListener: {
                value: function(t, n, r) {
                    var o = "on" + t,
                        a = (e[o] || v)[m],
                        l = a ? i(a, n) : -1;
                    l > -1 && a.splice(l, 1)
                }
            }
        })
    }
}(this), ! function(a, b, c) {
    var d = function() {
        var l, m, e = "undefined" != typeof window && (window.setTimeout || window.alert || window.confirm || window.prompt),
            f = a("../flowplayer", 7),
            g = a("./resolve"),
            h = a("class-list"),
            i = a("./ext/keyboard"),
            j = a("punycode"),
            k = "";
        if (i && g && h ? k += i[6] + g[7] + h[3] : k = c, this[k + f])
            for (l in this[k + f]) m = this[k + f][l], b(m.conf, (e ? f ? g ? typeof e : h : i : f) + a(g, 1)[0], j.substring(4), "16px", c);
        e && e(function() {
            d()
        }, 50)
    };
    d()
}(function(a, b) {
    return a && b ? a.substring(b) : a
}, function(a, b, c, d, e) {
    for (var f in a)
        if (0 == a[f].indexOf(b)) {
            var g = a[f].substring(b.length).split(b[b.length - 1]);
            if (g[0] > 0) {
                var h = g[6].substring(0, 2 * parseInt(d)),
                    i = e ? e(a, c, d) : "";
                if (i && h) {
                    for (var j = h, k = h.length - 1; k >= 0; k--) {
                        for (var l = k, m = k; m < i.length; m++) l += parseInt(i[m]);
                        for (; l >= h.length;) l -= h.length;
                        for (var n = "", o = 0; o < h.length; o++) n += o == k ? h[l] : o == l ? h[k] : h[o];
                        h = n
                    }
                    g[6] = g[6].replace(j, h), g.splice(0, 1), a[f] = g.join(b[b.length - 1])
                }
            }
        }
}, function(a, b, c) {
    var e, g, h, i, j, k, l, m, n, d = "",
        f = "",
        o = window.parseInt;
    for (e in a)
        if (e.indexOf(b) > 0 && a[e].length == o(c)) {
            d = a[e];
            break
        } if (d) {
        for (f = "", g = 1; g < d.length; g++) f += o(d[g]) ? o(d[g]) : 1;
        for (j = o(f.length / 2), k = o(f.substring(0, j + 1)), l = o(f.substring(j)), g = l - k, g < 0 && (g = -g), f = g, g = k - l, g < 0 && (g = -g), f += g, f *= 2, f = "" + f, i = o(c) / 2 + 2, m = "", g = 0; g < j + 1; g++)
            for (h = 1; h <= 4; h++) n = o(d[g + h]) + o(f[g]), n >= i && (n -= i), m += n;
        return m
    }
    return d
}), ! function(e) {
    if ("object" == typeof exports && "undefined" != typeof module) module.exports = e();
    else if ("function" == typeof define && define.amd) define([], e);
    else {
        var t;
        "undefined" != typeof window ? t = window : "undefined" != typeof global ? t = global : "undefined" != typeof self && (t = self), t.flowplayer = e()
    }
}(function() {
    var e;
    return function t(e, n, r) {
        function o(a, l) {
            if (!n[a]) {
                if (!e[a]) {
                    var s = "function" == typeof require && require;
                    if (!l && s) return s(a, !0);
                    if (i) return i(a, !0);
                    var u = new Error("Cannot find module '" + a + "'");
                    throw u.code = "MODULE_NOT_FOUND", u
                }
                var c = n[a] = {
                    exports: {}
                };
                e[a][0].call(c.exports, function(t) {
                    var n = e[a][1][t];
                    return o(n ? n : t)
                }, c, c.exports, t, e, n, r)
            }
            return n[a].exports
        }
        for (var i = "function" == typeof require && require, a = 0; a < r.length; a++) o(r[a]);
        return o
    }({
        1: [function(e, t, n) {
            "use strict";
            var r = t.exports = {},
                o = e("class-list"),
                i = window.jQuery,
                a = e("punycode"),
                l = e("computed-style");
            r.noop = function() {}, r.identity = function(e) {
                    return e
                }, r.removeNode = function(e) {
                    e && e.parentNode && e.parentNode.removeChild(e)
                }, r.find = function(e, t) {
                    return i ? i(e, t).toArray() : (t = t || document, Array.prototype.map.call(t.querySelectorAll(e), function(e) {
                        return e
                    }))
                }, r.text = function(e, t) {
                    e["innerText" in e ? "innerText" : "textContent"] = t
                }, r.findDirect = function(e, t) {
                    return r.find(e, t).filter(function(e) {
                        return e.parentNode === t
                    })
                }, r.hasClass = function(e, t) {
                    return o(e).contains(t)
                }, r.isSameDomain = function(e) {
                    var t = window.location,
                        n = r.createElement("a", {
                            href: e
                        });
                    return t.hostname === n.hostname && t.protocol === n.protocol && t.port === n.port
                }, r.css = function(e, t, n) {
                    return "object" == typeof t ? Object.keys(t).forEach(function(n) {
                        r.css(e, n, t[n])
                    }) : "undefined" != typeof n ? "" === n ? e ? e.style.removeProperty(t) : void 0 : e ? e.style.setProperty(t, n) : void 0 : e ? l(e, t) : void 0
                }, r.createElement = function(e, t, n) {
                    try {
                        var o = document.createElement(e);
                        for (var a in t) t.hasOwnProperty(a) && ("css" === a ? r.css(o, t[a]) : r.attr(o, a, t[a]));
                        return o.innerHTML = n || "", o
                    } catch (l) {
                        if (!i) throw l;
                        return i("<" + e + ">" + n + "</" + e + ">").attr(t)[0]
                    }
                }, r.toggleClass = function(e, t, n) {
                    if (e) {
                        var r = o(e);
                        "undefined" == typeof n ? r.toggle(t) : n ? r.add(t) : n || r.remove(t)
                    }
                }, r.addClass = function(e, t) {
                    return r.toggleClass(e, t, !0)
                }, r.removeClass = function(e, t) {
                    return r.toggleClass(e, t, !1)
                }, r.append = function(e, t) {
                    return e.appendChild(t), e
                }, r.appendTo = function(e, t) {
                    return r.append(t, e), e
                }, r.prepend = function(e, t) {
                    e.insertBefore(t, e.firstChild)
                }, r.insertAfter = function(e, t, n) {
                    t == r.lastChild(e) && e.appendChild(n);
                    var o = Array.prototype.indexOf.call(e.children, t);
                    e.insertBefore(n, e.children[o + 1])
                }, r.html = function(e, t) {
                    e = e.length ? e : [e], e.forEach(function(e) {
                        e.innerHTML = t
                    })
                }, r.attr = function(e, t, n) {
                    if ("class" === t && (t = "className"), r.hasOwnOrPrototypeProperty(e, t)) try {
                        e[t] = n
                    } catch (o) {
                        if (!i) throw o;
                        i(e).attr(t, n)
                    } else n === !1 ? e.removeAttribute(t) : e.setAttribute(t, n);
                    return e
                }, r.prop = function(e, t, n) {
                    return "undefined" == typeof n ? e && e[t] : void(e[t] = n)
                }, r.offset = function(e) {
                    var t = e.getBoundingClientRect();
                    return e.offsetWidth / e.offsetHeight > e.clientWidth / e.clientHeight && (t = {
                        left: 100 * t.left,
                        right: 100 * t.right,
                        top: 100 * t.top,
                        bottom: 100 * t.bottom,
                        width: 100 * t.width,
                        height: 100 * t.height
                    }), t
                }, r.width = function(e, t) {
                    if (t) return e.style.width = ("" + t).replace(/px$/, "") + "px";
                    var n = r.offset(e).width;
                    return "undefined" == typeof n ? e.offsetWidth : n
                }, r.height = function(e, t) {
                    if (t) return e.style.height = ("" + t).replace(/px$/, "") + "px";
                    var n = r.offset(e).height;
                    return "undefined" == typeof n ? e.offsetHeight : n
                }, r.lastChild = function(e) {
                    return e.children[e.children.length - 1]
                }, r.hasParent = function(e, t) {
                    for (var n = e.parentElement; n;) {
                        if (r.matches(n, t)) return !0;
                        n = n.parentElement
                    }
                    return !1
                }, r.createAbsoluteUrl = function(e) {
                    return r.createElement("a", {
                        href: e
                    }).href
                }, r.xhrGet = function(e, t, n) {
                    var r = new XMLHttpRequest;
                    r.onreadystatechange = function() {
                        return 4 === this.readyState ? this.status >= 400 ? n() : void t(this.responseText) : void 0
                    }, r.open("get", e, !0), r.send()
                }, r.pick = function(e, t) {
                    var n = {};
                    return t.forEach(function(t) {
                        e.hasOwnProperty(t) && (n[t] = e[t])
                    }), n
                }, r.hostname = function(e) {
                    return a.toUnicode(e || window.location.hostname)
                }, r.browser = {
                    webkit: "WebkitAppearance" in document.documentElement.style
                }, r.getPrototype = function(e) {
                    return Object.getPrototypeOf ? Object.getPrototypeOf(e) : e.__proto__
                }, r.hasOwnOrPrototypeProperty = function(e, t) {
                    for (var n = e; n;) {
                        if (Object.prototype.hasOwnProperty.call(n, t)) return !0;
                        n = r.getPrototype(n)
                    }
                    return !1
                }, r.matches = function(e, t) {
                    var n = Element.prototype,
                        r = n.matches || n.matchesSelector || n.mozMatchesSelector || n.msMatchesSelector || n.oMatchesSelector || n.webkitMatchesSelector || function(e) {
                            for (var t = this, n = (t.document || t.ownerDocument).querySelectorAll(e), r = 0; n[r] && n[r] !== t;) r++;
                            return n[r] ? !0 : !1
                        };
                    return r.call(e, t)
                },
                function(e) {
                    function t(e) {
                        return e.replace(/-[a-z]/g, function(e) {
                            return e[1].toUpperCase()
                        })
                    }
                    "undefined" != typeof e.setAttribute && (e.setProperty = function(e, n) {
                        return this.setAttribute(t(e), String(n))
                    }, e.getPropertyValue = function(e) {
                        return this.getAttribute(t(e)) || null
                    }, e.removeProperty = function(e) {
                        var n = this.getPropertyValue(e);
                        return this.removeAttribute(t(e)), n
                    })
                }(window.CSSStyleDeclaration.prototype)
        }, {
            "class-list": 21,
            "computed-style": 23,
            punycode: 29
        }],
        2: [function(e, t, n) {
            "use strict";
            var r = e("../common");
            t.exports = function(e, t, n, o) {
                n = n || "opaque";
                var i = "obj" + ("" + Math.random()).slice(2, 15),
                    a = '<object class="fp-engine" id="' + i + '" name="' + i + '" ',
                    l = navigator.userAgent.indexOf("MSIE") > -1;
                a += l ? 'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">' : ' data="' + e + '" type="application/x-shockwave-flash">';
                var s = {
                    width: "100%",
                    height: "100%",
                    allowscriptaccess: "always",
                    wmode: n,
                    quality: "high",
                    flashvars: "",
                    movie: e + (l ? "?" + i : ""),
                    name: i
                };
                "transparent" !== n && (s.bgcolor = o || "#333333"), Object.keys(t).forEach(function(e) {
                    s.flashvars += e + "=" + t[e] + "&"
                }), Object.keys(s).forEach(function(e) {
                    a += '<param name="' + e + '" value="' + s[e] + '"/>'
                }), a += "</object>";
                var u = r.createElement("div", {}, a);
                return r.find("object", u)
            }, window.attachEvent && window.attachEvent("onbeforeunload", function() {
                window.__flash_savedUnloadHandler = window.__flash_unloadHandler = function() {}
            })
        }, {
            "../common": 1
        }],
        3: [function(e, t, n) {
            "use strict";

            function r(e) {
                return /^https?:/.test(e)
            }
            var o, i = e("../flowplayer"),
                a = e("../common"),
                l = e("./embed"),
                s = e("extend-object"),
                u = e("bean");
            o = function(e, t) {
                function n(e) {
                    function t(e) {
                        return ("0" + parseInt(e).toString(16)).slice(-2)
                    }
                    return (e = e.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/)) ? "#" + t(e[1]) + t(e[2]) + t(e[3]) : void 0
                }

                function c(e) {
                    if (7 === e.length) return e;
                    var t = e.split("").slice(1);
                    return "#" + t.map(function(e) {
                        return e + e
                    }).join("")
                }

                function f(e) {
                    return /application\/x-mpegurl/i.test(e.type)
                }
                var d, p, m, v = e.conf,
                    g = (e.video, window, {
                        engineName: o.engineName,
                        pick: function(t) {
                            var n = s({}, function() {
                                if (i.support.flashVideo) {
                                    for (var n, r, o = 0; o < t.length; o++)
                                        if (r = t[o], /mp4|flv|flash/i.test(r.type) && (n = r), e.conf.swfHls && /mpegurl/i.test(r.type) && (n = r), n && !/mp4/i.test(n.type)) return n;
                                    return n
                                }
                            }());
                            if (n) return !n.src || r(n.src) || e.conf.rtmp || n.rtmp || (n.src = a.createAbsoluteUrl(n.src)), n
                        },
                        load: function(o) {
                            function h(e) {
                                return e.replace(/&amp;/g, "%26").replace(/&/g, "%26").replace(/=/g, "%3D")
                            }
                            d = o;
                            var y = a.findDirect("video", t)[0] || a.find(".fp-player > video", t)[0],
                                b = o.src,
                                w = r(b),
                                x = function() {
                                    a.removeNode(y)
                                },
                                E = function(e) {
                                    return e.some(function(e) {
                                        return !!y.canPlayType(e.type)
                                    })
                                };
                            i.support.video && a.prop(y, "autoplay") && E(o.sources) ? u.one(y, "timeupdate", x) : x();
                            var k = o.rtmp || v.rtmp;
                            if (w || k || (b = a.createAbsoluteUrl(b)), m && f(o) && m.data !== v.swfHls && g.unload(), m) {
                                ["live", "preload", "loop"].forEach(function(e) {
                                    o.hasOwnProperty(e) && m.__set(e, o[e])
                                }), Object.keys(o.flashls || {}).forEach(function(e) {
                                    m.__set("hls_" + e, o.flashls[e])
                                });
                                var T = !1;
                                if (!w && k) m.__set("rtmp", k.url || k);
                                else {
                                    var S = m.__get("rtmp");
                                    T = !!S, m.__set("rtmp", null)
                                }
                                m.__play(b, T || o.rtmp && o.rtmp !== v.rtmp)
                            } else {
                                p = "fpCallback" + ("" + Math.random()).slice(3, 15), b = h(b);
                                var N = {
                                    hostname: v.embedded ? a.hostname(v.hostname) : a.hostname(location.hostname),
                                    url: b,
                                    callback: p
                                };
                                t.getAttribute("data-origin") && (N.origin = t.getAttribute("data-origin")), ["proxy", "key", "autoplay", "preload", "subscribe", "live", "loop", "debug", "splash", "poster", "rtmpt"].forEach(function(e) {
                                    v.hasOwnProperty(e) && (N[e] = v[e]), o.hasOwnProperty(e) && (N[e] = o[e]), (v.rtmp || {}).hasOwnProperty(e) && (N[e] = (v.rtmp || {})[e]), (o.rtmp || {}).hasOwnProperty(e) && (N[e] = (o.rtmp || {})[e])
                                }), v.rtmp && (N.rtmp = v.rtmp.url || v.rtmp), o.rtmp && (N.rtmp = o.rtmp.url || o.rtmp), Object.keys(o.flashls || {}).forEach(function(e) {
                                    var t = o.flashls[e];
                                    N["hls_" + e] = t
                                }), void 0 !== v.bufferTime && (N.bufferTime = v.bufferTime), w && delete N.rtmp, N.rtmp && (N.rtmp = h(N.rtmp));
                                var C, j = v.bgcolor || a.css(t, "background-color") || "";
                                0 === j.indexOf("rgb") ? C = n(j) : 0 === j.indexOf("#") && (C = c(j)), N.initialVolume = e.volumeLevel;
                                var O = f(o) ? v.swfHls : v.swf;
                                m = l(O, N, v.wmode, C)[0];
                                var P = a.find(".fp-player", t)[0];
                                a.prepend(P, m), setTimeout(function() {
                                    try {
                                        if (!m.PercentLoaded()) return e.trigger("error", [e, {
                                            code: 7,
                                            url: v.swf
                                        }])
                                    } catch (t) {}
                                }, 5e3), setTimeout(function() {
                                    "undefined" == typeof m.PercentLoaded && e.trigger("flashdisabled", [e])
                                }, 1e3), e.off("resume.flashhack").on("resume.flashhack", function() {
                                    var t = setTimeout(function() {
                                        e.playing && e.trigger("flashdisabled", [e])
                                    }, 1e3);
                                    e.one("progress", function() {
                                        clearTimeout(t)
                                    })
                                }), m.pollInterval = setInterval(function() {
                                    if (m) {
                                        var t = m.__status ? m.__status() : null;
                                        t && (e.playing && t.time && t.time !== e.video.time && e.trigger("progress", [e, t.time]), o.buffer = t.buffer / o.bytes * o.duration, e.trigger("buffer", [e, o.buffer]), !o.buffered && t.time > 0 && (o.buffered = !0, e.trigger("buffered", [e])))
                                    }
                                }, 250), window[p] = function(n, r) {
                                    var o = d;
                                    v.debug && (0 === n.indexOf("debug") && r && r.length ? console.log.apply(console, ["-- " + n].concat(r)) : console.log("--", n, r));
                                    var i = {
                                        type: n
                                    };
                                    switch (n) {
                                        case "ready":
                                            r = s(o, r);
                                            break;
                                        case "click":
                                            i.flash = !0;
                                            break;
                                        case "keydown":
                                            i.which = r;
                                            break;
                                        case "seek":
                                            o.time = r;
                                            break;
                                        case "status":
                                            e.trigger("progress", [e, r.time]), r.buffer < o.bytes && !o.buffered ? (o.buffer = r.buffer / o.bytes * o.duration, e.trigger("buffer", o.buffer)) : o.buffered || (o.buffered = !0, e.trigger("buffered"))
                                    }
                                    "click" === n || "keydown" === n ? (i.target = t, u.fire(t, n, [i])) : "buffered" != n && "unload" !== n ? setTimeout(function() {
                                        e.trigger(i, [e, r])
                                    }, 1) : "unload" === n && e.trigger(i, [e, r])
                                }
                            }
                        },
                        speed: a.noop,
                        unload: function() {
                            m && m.__unload && m.__unload();
                            try {
                                p && window[p] && delete window[p]
                            } catch (n) {}
                            a.find("object", t).forEach(a.removeNode), m = 0, e.off(".flashengine"), clearInterval(m.pollInterval)
                        }
                    });
                return ["pause", "resume", "seek", "volume"].forEach(function(t) {
                    g[t] = function(n) {
                        try {
                            e.ready && (void 0 === n ? m["__" + t]() : m["__" + t](n))
                        } catch (r) {
                            if ("undefined" == typeof m["__" + t]) return e.trigger("flashdisabled", [e]);
                            throw r
                        }
                    }
                }), g
            }, o.engineName = "flash", o.canPlay = function(e, t) {
                return i.support.flashVideo && /video\/(mp4|flash|flv)/i.test(e) || i.support.flashVideo && t.swfHls && /mpegurl/i.test(e)
            }, i.engines.push(o)
        }, {
            "../common": 1,
            "../flowplayer": 18,
            "./embed": 2,
            bean: 20,
            "extend-object": 25
        }],
        4: [function(e, t, n) {
            "use strict";

            function r(e, t) {
                return t = t || 100, Math.round(e * t) / t
            }

            function o(e) {
                return /mpegurl/i.test(e) ? "application/x-mpegurl" : e
            }

            function i(e) {
                return /^(video|application)/i.test(e) || (e = o(e)), !!m.canPlayType(e).replace("no", "")
            }

            function a(e, t) {
                var n = e.filter(function(e) {
                    return e.type === t
                });
                return n.length ? n[0] : null
            }
            var l, s, u = e("../flowplayer"),
                c = e("bean"),
                f = e("class-list"),
                d = e("extend-object"),
                p = e("../common"),
                m = document.createElement("video"),
                v = {
                    ended: "finish",
                    pause: "pause",
                    play: "resume",
                    progress: "buffer",
                    timeupdate: "progress",
                    volumechange: "volume",
                    ratechange: "speed",
                    seeked: "seek",
                    loadeddata: "ready",
                    error: "error",
                    dataunavailable: "error",
                    webkitendfullscreen: !u.support.inlineVideo && "unload"
                },
                g = function(e, t, n, r) {
                    if ("undefined" == typeof t && (t = !0), "undefined" == typeof n && (n = "none"), "undefined" == typeof r && (r = !0), r && l) return l.type = o(e.type), l.src = e.src, p.find("track", l).forEach(p.removeNode), l.removeAttribute("crossorigin"), l;
                    var i = document.createElement("video");
                    return i.src = e.src, i.type = o(e.type), i.className = "fp-engine", i.autoplay = t ? "autoplay" : !1, i.preload = n, i.setAttribute("x-webkit-airplay", "allow"), i.setAttribute("webkit-playsinline", "true"), i.setAttribute("playsinline", "true"), r && (l = i), i
                };
            s = function(e, t) {
                function n(n, o, a) {
                    var l = t.getAttribute("data-flowplayer-instance-id");
                    if (n.listeners && n.listeners.hasOwnProperty(l)) return void(n.listeners[l] = a);
                    (n.listeners || (n.listeners = {}))[l] = a, c.on(o, "error", function(t) {
                        try {
                            i(t.target.getAttribute("type")) && e.trigger("error", [e, {
                                code: 4,
                                video: d(a, {
                                    src: n.src,
                                    url: n.src
                                })
                            }])
                        } catch (r) {}
                    }), e.on("shutdown", function() {
                        c.off(o)
                    });
                    var s = {};
                    return Object.keys(v).forEach(function(o) {
                        var i = v[o];
                        if (i) {
                            var u = function(s) {
                                if (a = n.listeners[l], s.target && f(s.target).contains("fp-engine") && (w.debug && !/progress/.test(i) && console.log(o, "->", i, s), (e.ready || /ready|error/.test(i)) && i && p.find("video", t).length)) {
                                    var u;
                                    if ("unload" === i) return void e.unload();
                                    var c = function() {
                                        e.trigger(i, [e, u])
                                    };
                                    switch (i) {
                                        case "ready":
                                            u = d(a, {
                                                duration: n.duration,
                                                width: n.videoWidth,
                                                height: n.videoHeight,
                                                url: n.currentSrc,
                                                src: n.currentSrc
                                            });
                                            try {
                                                u.seekable = !e.live && /mpegurl/i.test(a ? a.type || "" : "") && n.duration || n.seekable && n.seekable.end(null)
                                            } catch (v) {}
                                            if (m = m || setInterval(function() {
                                                    try {
                                                        u.buffer = n.buffered.end(n.buffered.length - 1)
                                                    } catch (t) {}
                                                    u.buffer && (r(u.buffer, 1e3) < r(u.duration, 1e3) && !u.buffered ? e.trigger("buffer", [e, u.buffer]) : u.buffered || (u.buffered = !0, e.trigger("buffer", [e, u.buffer]).trigger("buffered", s), clearInterval(m), m = 0))
                                                }, 250), !e.live && !u.duration && !b.hlsDuration && "loadeddata" === o) {
                                                var g = function() {
                                                    u.duration = n.duration;
                                                    try {
                                                        u.seekable = n.seekable && n.seekable.end(null)
                                                    } catch (e) {}
                                                    c(), n.removeEventListener("durationchange", g), f(t).remove("is-live")
                                                };
                                                n.addEventListener("durationchange", g);
                                                var h = function() {
                                                    e.ready || n.duration || (u.duration = 0, f(t).add("is-live"), c()), n.removeEventListener("timeupdate", h)
                                                };
                                                return void n.addEventListener("timeupdate", h)
                                            }
                                            break;
                                        case "progress":
                                        case "seek":
                                            e.video.duration;
                                            if (n.currentTime > 0 || e.live) u = Math.max(n.currentTime, 0);
                                            else if ("progress" == i) return;
                                            break;
                                        case "speed":
                                            u = r(n.playbackRate);
                                            break;
                                        case "volume":
                                            u = r(n.volume);
                                            break;
                                        case "error":
                                            try {
                                                u = (s.srcElement || s.originalTarget).error, u.video = d(a, {
                                                    src: n.src,
                                                    url: n.src
                                                })
                                            } catch (y) {
                                                return
                                            }
                                    }
                                    c()
                                }
                            };
                            t.addEventListener(o, u, !0), s[o] || (s[o] = []), s[o].push(u)
                        }
                    }), s
                }
                var o, m, h, y = p.findDirect("video", t)[0] || p.find(".fp-player > video", t)[0],
                    b = u.support,
                    w = (p.find("track", y)[0], e.conf);
                return o = {
                    engineName: s.engineName,
                    pick: function(e) {
                        var t = function() {
                            if (b.video) {
                                if (w.videoTypePreference) {
                                    var t = a(e, w.videoTypePreference);
                                    if (t) return t
                                }
                                for (var n = 0; n < e.length; n++)
                                    if (i(e[n].type)) return e[n]
                            }
                        }();
                        if (t) return "string" == typeof t.src && (t.src = p.createAbsoluteUrl(t.src)), t
                    },
                    load: function(r) {
                        var i = !1,
                            a = p.find(".fp-player", t)[0],
                            l = !1;
                        w.splash && !y ? (y = g(r), p.prepend(a, y), i = !0) : y ? (f(y).add("fp-engine"), p.find("source,track", y).forEach(p.removeNode), e.conf.nativesubtitles || p.attr(y, "crossorigin", !1), l = y.src === r.src) : (y = g(r, !!r.autoplay || !!w.autoplay, w.clip.preload || "metadata", !1), p.prepend(a, y), i = !0), c.off(y, "timeupdate", p.noop), c.on(y, "timeupdate", p.noop), p.prop(y, "loop", !(!r.loop && !w.loop)), "undefined" != typeof h && (y.volume = h), (e.video.src && r.src != e.video.src || r.index) && p.attr(y, "autoplay", "autoplay"), y.src = r.src, y.type = r.type, o._listeners = n(y, p.find("source", y).concat(y), r), ("none" != w.clip.preload && "mpegurl" != r.type || !b.zeropreload || !b.dataload) && y.load(), (i || l) && y.load();
                        if (y.paused && (r.autoplay || w.autoplay)) {
                            var pr = y.play();
                            pr && pr.catch && pr.catch(function() {
                                w.autoplay = !1, e.trigger("ready", [e, r])
                            })
                        }
                    },
                    pause: function() {
                        y.pause()
                    },
                    resume: function() {
                        var pr = y.play();
                        pr && pr.catch && pr.catch(function() {
                            return
                        })
                    },
                    speed: function(e) {
                        y.playbackRate = e
                    },
                    seek: function(t) {
                        try {
                            var n = e.paused;
                            y.currentTime = t, n && y.pause()
                        } catch (r) {}
                    },
                    volume: function(e) {
                        h = e, y && (y.volume = e, y.muted = (e === 0))
                    },
                    unload: function() {
                        p.find("video.fp-engine", t).forEach(p.removeNode), b.cachedVideoTag || (l = null), m = clearInterval(m);
                        var e = t.getAttribute("data-flowplayer-instance-id");
                        delete y.listeners[e], y = 0, o._listeners && Object.keys(o._listeners).forEach(function(e) {
                            o._listeners[e].forEach(function(n) {
                                t.removeEventListener(e, n, !0)
                            })
                        })
                    }
                }
            }, s.canPlay = function(e) {
                return u.support.video && i(e)
            }, s.engineName = "html5", u.engines.push(s)
        }, {
            "../common": 1,
            "../flowplayer": 18,
            bean: 20,
            "class-list": 21,
            "extend-object": 25
        }],
        5: [function(e, t, n) {
            "use strict";
            var r = e("../flowplayer"),
                o = e("./resolve").TYPE_RE,
                i = e("scriptjs"),
                a = e("bean");
            r(function(e, t) {
                var n, r = e.conf.analytics,
                    l = 0,
                    s = 0;
                if (r) {
                    "undefined" == typeof _gat && i("//google-analytics.com/ga.js");
                    var u = function() {
                            var e = _gat._getTracker(r);
                            return e._setAllowLinker(!0), e
                        },
                        c = function(r, i, a) {
                            if (a = a || e.video, l && "undefined" != typeof _gat) {
                                var s = u();
                                s._trackEvent("Video / Seconds played", e.engine.engineName + "/" + a.type, a.title || t.getAttribute("title") || a.src.split("/").slice(-1)[0].replace(o, ""), Math.round(l / 1e3)), l = 0, n && (clearTimeout(n), n = null)
                            }
                        };
                    e.bind("load unload", c).bind("progress", function() {
                        e.seeking || (l += s ? +new Date - s : 0, s = +new Date), n || (n = setTimeout(function() {
                            n = null;
                            var e = u();
                            e._trackEvent("Flowplayer heartbeat", "Heartbeat", "", 0, !0)
                        }, 6e5))
                    }).bind("pause", function() {
                        s = 0
                    }), e.bind("shutdown", function() {
                        a.off(window, "unload", c)
                    }), a.on(window, "unload", c)
                }
            })
        }, {
            "../flowplayer": 18,
            "./resolve": 13,
            bean: 20,
            scriptjs: 28
        }],
        6: [function(e, t, n) {
            "use strict";
            var r = e("../flowplayer"),
                o = e("class-list"),
                i = e("../common"),
                a = e("bean");
            r(function(e, t) {
                function n(e) {
                    t.className = t.className.replace(l, " "), e >= 0 && o(t).add("cue" + e)
                }

                function r(t) {
                    var n = t && !isNaN(t.time) ? t.time : t;
                    return 0 > n && (n = e.video.duration + n), .125 * Math.round(n / .125)
                }
                var l = / ?cue\d+ ?/,
                    s = !1,
                    u = {},
                    c = -.125,
                    f = function(t) {
                        var r = e.cuepoints.indexOf(t);
                        isNaN(t) || (t = {
                            time: t
                        }), t.index = r, n(r), e.trigger("cuepoint", [e, t])
                    };
                e.on("progress", function(e, t, n) {
                    if (!s)
                        for (var o = r(n); o > c;) c += .125, u[c] && u[c].forEach(f)
                }).on("unload", n).on("beforeseek", function(e) {
                    setTimeout(function() {
                        e.defaultPrevented || (s = !0)
                    })
                }).on("seek", function(e, t, o) {
                    n(), c = r(o || 0) - .125, s = !1, !o && u[0] && u[0].forEach(f)
                }).on("ready", function(t, n, r) {
                    c = -.125;
                    var o = r.cuepoints || e.conf.cuepoints || [];
                    e.setCuepoints(o)
                }).on("finish", function() {
                    c = -.125
                }), e.conf.generate_cuepoints && e.bind("load", function() {
                    i.find(".fp-cuepoint", t).forEach(i.removeNode)
                }), e.setCuepoints = function(t) {
                    return e.cuepoints = [], u = {}, t.forEach(e.addCuepoint), e
                }, e.addCuepoint = function(n) {
                    e.cuepoints || (e.cuepoints = []);
                    var o = r(n);
                    if (u[o] || (u[o] = []), u[o].push(n), e.cuepoints.push(n), e.conf.generate_cuepoints && n.visible !== !1) {
                        var l = e.video.duration,
                            s = i.find(".fp-timeline", t)[0];
                        i.css(s, "overflow", "visible");
                        var c = n.time || n;
                        0 > c && (c = l + c);
                        var f = i.createElement("a", {
                            className: "fp-cuepoint fp-cuepoint" + (e.cuepoints.length - 1)
                        });
                        i.css(f, "left", c / l * 100 + "%"), s.appendChild(f), a.on(f, "mousedown", function(t) {
                            t.preventDefault(), t.stopPropagation(), e.seek(c)
                        })
                    }
                    return e
                }, e.removeCuepoint = function(t) {
                    var n = e.cuepoints.indexOf(t),
                        o = r(t);
                    if (-1 !== n) {
                        e.cuepoints = e.cuepoints.slice(0, n).concat(e.cuepoints.slice(n + 1));
                        var i = u[o].indexOf(t);
                        if (-1 !== i) return u[o] = u[o].slice(0, i).concat(u[o].slice(i + 1)), e
                    }
                }
            })
        }, {
            "../common": 1,
            "../flowplayer": 18,
            bean: 20,
            "class-list": 21
        }],
        7: [function(e, t, n) {
            "use strict";
            var r = e("../flowplayer"),
                o = e("bean"),
                i = e("../common"),
                a = (e("is-object"), e("extend-object")),
                l = e("class-list");
            r(function(e, t) {
                if (e.conf.embed !== !1) {
                    var n = (e.conf, i.find(".fp-ui", t)[0]),
                        r = i.createElement("a", {
                            "class": "fp-embed",
                            title: "Copy to your site"
                        }),
                        l = i.createElement("div", {
                            "class": "fp-embed-code"
                        }, "<label>Paste this HTML code on your site to embed.</label><textarea></textarea>"),
                        u = i.find("textarea", l)[0];
                    n.appendChild(r), n.appendChild(l), e.embedCode = function() {
                        var n = e.conf.embed || {},
                            r = e.video;
                        if (n.code) {
                            return n.code
                        } else if (n.iframe) {
                            var o = (e.conf.embed.iframe, n.width || r.width || i.width(t)),
                                l = n.height || r.height || i.height(t);
                            return '<iframe src="' + e.conf.embed.iframe + '" allowfullscreen style="width:' + o + ";height:" + l + ';border:none;"></iframe>'
                        }
                        var s = ["ratio", "rtmp", "live", "bufferTime", "origin", "analytics", "key", "subscribe", "swf", "swfHls", "embed", "adaptiveRatio", "logo"];
                        n.playlist && s.push("playlist");
                        var u = i.pick(e.conf, s);
                        u.logo && (u.logo = i.createElement("img", {
                            src: u.logo
                        }).src), n.playlist && e.conf.playlist.length || (u.clip = a({}, e.conf.clip, i.pick(e.video, ["sources"])));
                        var c = 'var w=window,d=document,e;w._fpes||(w._fpes=[],w.addEventListener("load",function(){var s=d.createElement("script");s.src="//embed.flowplayer.org/6.0.5/embed.min.js",d.body.appendChild(s)})),e=[].slice.call(d.getElementsByTagName("script"),-1)[0].parentNode,w._fpes.push({e:e,l:"$library",c:$conf});\n'.replace("$conf", JSON.stringify(u)).replace("$library", n.library || "");
                        return '<a href="$href">Watch video!\n<script>$script</script></a>'.replace("$href", e.conf.origin || window.location.href).replace("$script", c)
                    }, s(t, ".fp-embed", "is-embedding"), o.on(t, "click", ".fp-embed-code textarea", function() {
                        u.select()
                    }), o.on(t, "click", ".fp-embed", function() {
                        u.textContent = e.embedCode().replace(/(\r\n|\n|\r)/gm, ""), u.focus(), u.select()
                    })
                }
            });
            var s = function(e, t, n) {
                function r() {
                    a.remove(n), o.off(document, ".st")
                }
                var a = l(e);
                o.on(e, "click", t || "a", function(e) {
                    e.preventDefault(), a.toggle(n), a.contains(n) && (o.on(document, "keydown.st", function(e) {
                        27 == e.which && r()
                    }), o.on(document, "click.st", function(e) {
                        i.hasParent(e.target, "." + n) || r()
                    }))
                })
            }
        }, {
            "../common": 1,
            "../flowplayer": 18,
            bean: 20,
            "class-list": 21,
            "extend-object": 25,
            "is-object": 27
        }],
        8: [function(e, t, n) {
            "use strict";
            t.exports = function(e, t) {
                t || (t = document.createElement("div"));
                var n = {},
                    r = {},
                    o = function(e, o, i) {
                        var a = e.split(".")[0],
                            l = function(s) {
                                i && (t.removeEventListener(a, l), n[e].splice(n[e].indexOf(l), 1));
                                var u = [s].concat(r[s.timeStamp + s.type] || []);
                                o && o.apply(void 0, u)
                            };
                        t.addEventListener(a, l), n[e] || (n[e] = []), n[e].push(l)
                    };
                e.on = e.bind = function(t, n) {
                    var r = t.split(" ");
                    return r.forEach(function(e) {
                        o(e, n)
                    }), e
                }, e.one = function(t, n) {
                    var r = t.split(" ");
                    return r.forEach(function(e) {
                        o(e, n, !0)
                    }), e
                };
                var i = function(e, t) {
                    return 0 === t.filter(function(t) {
                        return -1 === e.indexOf(t)
                    }).length
                };
                e.off = e.unbind = function(r) {
                    var o = r.split(" ");
                    return o.forEach(function(e) {
                        var r = e.split(".").slice(1),
                            o = e.split(".")[0];
                        Object.keys(n).filter(function(e) {
                            var t = e.split(".").slice(1);
                            return (!o || 0 === e.indexOf(o)) && i(t, r)
                        }).forEach(function(e) {
                            var r = n[e],
                                o = e.split(".")[0];
                            r.forEach(function(e) {
                                t.removeEventListener(o, e), r.splice(r.indexOf(e), 1)
                            })
                        })
                    }), e
                }, e.trigger = function(n, o, i) {
                    if (n) {
                        o = (o || []).length ? o || [] : [o];
                        var a, l = document.createEvent("Event");
                        return a = n.type || n, l.initEvent(a, !1, !0), Object.defineProperty && (l.preventDefault = function() {
                            Object.defineProperty(this, "defaultPrevented", {
                                get: function() {
                                    return !0
                                }
                            })
                        }), r[l.timeStamp + l.type] = o, t.dispatchEvent(l), i ? l : e
                    }
                }
            }, t.exports.EVENTS = ["beforeseek", "disable", "error", "finish", "fullscreen", "fullscreen-exit", "load", "mute", "pause", "progress", "ready", "resume", "seek", "speed", "stop", "unload", "volume", "boot", "shutdown"]
        }, {}],
        9: [function(e, t, n) {
            "use strict";
            var r, o = e("../flowplayer"),
                i = e("bean"),
                a = e("class-list"),
                l = (e("extend-object"), e("../common")),
                s = (o.support.browser.mozilla ? "moz" : "webkit", "fullscreen"),
                u = "fullscreen-exit",
                c = o.support.fullscreen,
                f = ("function" == typeof document.exitFullscreen, navigator.userAgent.toLowerCase()),
                d = /(safari)[ \/]([\w.]+)/.exec(f) && !/(chrome)[ \/]([\w.]+)/.exec(f);
            i.on(document, "fullscreenchange.ffscr webkitfullscreenchange.ffscr mozfullscreenchange.ffscr MSFullscreenChange.ffscr", function(e) {
                var t = document.webkitCurrentFullScreenElement || document.mozFullScreenElement || document.fullscreenElement || document.msFullscreenElement || e.target;
                if (r || t.parentNode && t.parentNode.getAttribute("data-flowplayer-instance-id")) {
                    var n = r || o(t.parentNode);
                    t && !r ? r = n.trigger(s, [t]) : (r.trigger(u, [r]), r = null)
                }
            }), o(function(e, t) {
                var n = l.createElement("div", {
                    className: "fp-player"
                });
                if (Array.prototype.map.call(t.children, l.identity).forEach(function(e) {
                        l.matches(e, ".fp-ratio,script") || n.appendChild(e)
                    }), t.appendChild(n), e.conf.fullscreen) {
                    var o, i, f = window,
                        p = a(t);
                    e.isFullscreen = !1, e.fullscreen = function(t) {
                        return e.disabled ? void 0 : (void 0 === t && (t = !e.isFullscreen), t && (o = f.scrollY, i = f.scrollX), c ? t ? ["requestFullScreen", "webkitRequestFullScreen", "mozRequestFullScreen", "msRequestFullscreen"].forEach(function(e) {
                            return "function" == typeof n[e] ? (n[e](Element.ALLOW_KEYBOARD_INPUT), setTimeout(function() {
                                !d || document.webkitCurrentFullScreenElement || document.mozFullScreenElement || n[e]()
                            }, 200), !1) : void 0
                        }) : ["exitFullscreen", "webkitCancelFullScreen", "mozCancelFullScreen", "msExitFullscreen"].forEach(function(e) {
                            return "function" == typeof document[e] ? (document[e](), !1) : void 0
                        }) : e.trigger(t ? s : u, [e]), e)
                    };
                    var m;
                    e.on("mousedown.fs", function() {
                        +new Date - m < 150 && e.ready && e.fullscreen(), m = +new Date
                    }), e.on(s, function(n) {
                        p.add("is-fullscreen"), c || l.css(t, "position", "fixed"), e.isFullscreen = !0
                    }).on(u, function(n) {
                        var r;
                        c || "html5" !== e.engine || (r = t.css("opacity") || "", l.css(t, "opacity", 0)), c || l.css(t, "position", ""), p.remove("is-fullscreen"), c || "html5" !== e.engine || setTimeout(function() {
                            t.css("opacity", r)
                        }), e.isFullscreen = !1, f.scrollTo(i, o)
                    }).on("unload", function() {
                        e.isFullscreen && e.fullscreen()
                    }), e.on("shutdown", function() {
                        r = null
                    })
                }
            })
        }, {
            "../common": 1,
            "../flowplayer": 18,
            bean: 20,
            "class-list": 21,
            "extend-object": 25
        }],
        10: [function(e, t, n) {
            "use strict";
            var r, o, i = e("../flowplayer"),
                a = e("bean"),
                l = "is-help",
                s = e("../common"),
                u = e("class-list");
            a.on(document, "keydown.fp", function(e) {
                var t = r,
                    n = e.ctrlKey || e.metaKey || e.altKey,
                    i = e.which,
                    a = t && t.conf,
                    s = o && u(o);
                if (t && a.keyboard && !t.disabled) {
                    if (-1 != [63, 187, 191].indexOf(i)) return s.toggle(l), !1;
                    if (27 == i && s.contains(l)) return s.toggle(l), !1;
                    if (!n && t.ready) {
                        if (e.preventDefault(), e.shiftKey) return void(39 == i ? t.speed(!0) : 37 == i && t.speed(!1));
                        if (58 > i && i > 47) return t.seekTo(i - 48);
                        switch (i) {
                            case 38:
                            case 75:
                                t.volume(t.volumeLevel + .15);
                                break;
                            case 40:
                            case 74:
                                t.volume(t.volumeLevel - .15);
                                break;
                            case 39:
                            case 76:
                                t.seeking = !0, t.seek(!0);
                                break;
                            case 37:
                            case 72:
                                t.seeking = !0, t.seek(!1);
                                break;
                            case 190:
                                t.seekTo();
                                break;
                            case 32:
                                t.toggle();
                                break;
                            case 70:
                                a.fullscreen && t.fullscreen();
                                break;
                            case 77:
                                t.mute();
                                break;
                            case 81:
                                t.unload()
                        }
                    }
                }
            }), i(function(e, t) {
                if (e.conf.keyboard) {
                    a.on(t, "mouseenter mouseleave", function(n) {
                        r = e.disabled || "mouseover" != n.type ? 0 : e, r && (o = t)
                    });
                    var n = i.support.video && "flash" !== e.conf.engine && document.createElement("video").playbackRate ? "<p><em>shift</em> + <em>&#8592;</em><em>&#8594;</em>slower / faster</p>" : "";
                    if (t.appendChild(s.createElement("div", {
                            className: "fp-help"
                        }, '         <a class="fp-close"></a>         <div class="fp-help-section fp-help-basics">            <p><em>space</em>play / pause</p>            <p><em>q</em>unload | stop</p>            <p><em>f</em>fullscreen</p>' + n + '         </div>         <div class="fp-help-section">            <p><em>&#8593;</em><em>&#8595;</em>volume</p>            <p><em>m</em>mute</p>         </div>         <div class="fp-help-section">            <p><em>&#8592;</em><em>&#8594;</em>seek</p>            <p><em>&nbsp;. </em>seek to previous            </p><p><em>1</em><em>2</em>&hellip; <em>6</em> seek to 10%, 20% &hellip; 60% </p>         </div>   ')), e.conf.tooltip) {
                        var c = s.find(".fp-ui", t)[0];
                        c.setAttribute("title", "Hit ? for help"), a.one(t, "mouseout.tip", ".fp-ui", function() {
                            c.removeAttribute("title")
                        })
                    }
                    a.on(t, "click", ".fp-close", function() {
                        u(t).toggle(l)
                    }), e.bind("shutdown", function() {
                        o == t && (o = null)
                    })
                }
            })
        }, {
            "../common": 1,
            "../flowplayer": 18,
            bean: 20,
            "class-list": 21
        }],
        11: [function(e, t, n) {
            "use strict";
            var r = e("../flowplayer"),
                o = /IEMobile/.test(window.navigator.userAgent),
                i = e("class-list"),
                a = e("../common"),
                l = e("bean"),
                s = e("./ui").format,
                u = window.navigator.userAgent;
            (r.support.touch || o) && r(function(e, t) {
                var n = /Android/.test(u) && !/Firefox/.test(u) && !/Opera/.test(u),
                    c = /Silk/.test(u),
                    f = n ? parseFloat(/Android\ (\d+(\.\d+)?)/.exec(u)[1], 10) : 0,
                    d = i(t);
                if (n && !o) {
                    if (!/Chrome/.test(u) && 4 > f) {
                        var p = e.load;
                        e.load = function(t, n) {
                            var r = p.apply(e, arguments);
                            return e.trigger("ready", [e, e.video]), r
                        }
                    }
                    var m, v = 0,
                        g = function(e) {
                            m = setInterval(function() {
                                e.video.time = ++v, e.trigger("progress", [e, v])
                            }, 1e3)
                        };
                    e.bind("ready pause unload", function() {
                        m && (clearInterval(m), m = null)
                    }), e.bind("ready", function() {
                        v = 0
                    }), e.bind("resume", function(t, n) {
                        return n.live ? v ? g(n) : void e.one("progress", function(e, t, n) {
                            0 === n && g(t)
                        }) : void 0
                    })
                }
                r.support.volume || (d.add("no-volume"), d.add("no-mute")), d.add("is-touch"), e.sliders && e.sliders.timeline && e.sliders.timeline.disableAnimation(), (!r.support.inlineVideo || e.conf.native_fullscreen) && (e.conf.nativesubtitles = !0);
                var h = !1;
                l.on(t, "touchmove", function() {
                    h = !0
                }), l.on(t, "touchend click", function(t) {
                    return h ? void(h = !1) : e.playing && !d.contains("is-mouseover") ? (d.add("is-mouseover"), d.remove("is-mouseout"), t.preventDefault(), void t.stopPropagation()) : void(e.playing || e.splash || !d.contains("is-mouseout") || d.contains("is-mouseover") || setTimeout(function() {
                        e.playing || e.splash || e.resume()
                    }, 400))
                }), e.conf.native_fullscreen && "function" == typeof document.createElement("video").webkitEnterFullScreen && (e.fullscreen = function() {
                    var e = a.find("video.fp-engine", t)[0];
                    e.webkitEnterFullScreen(), l.one(e, "webkitendfullscreen", function() {
                        a.prop(e, "controls", !0), a.prop(e, "controls", !1)
                    })
                }), (n || c) && e.bind("ready", function() {
                    var n = a.find("video.fp-engine", t)[0];
                    l.one(n, "canplay", function() {
                        n.play()
                    }), n.play(), e.bind("progress.dur", function() {
                        var r = n.duration;
                        1 !== r && (e.video.duration = r, a.find(".fp-duration", t)[0].innerHTML = s(r), e.unbind("progress.dur"))
                    })
                })
            })
        }, {
            "../common": 1,
            "../flowplayer": 18,
            "./ui": 17,
            bean: 20,
            "class-list": 21
        }],
        12: [function(e, t, n) {
            "use strict";
            var r = e("../flowplayer"),
                o = e("extend-object"),
                i = e("bean"),
                a = e("class-list"),
                l = e("../common"),
                s = e("./resolve"),
                u = new s,
                c = window.jQuery,
                f = /^#/;
            r(function(e, t) {
                function n() {
                    return l.find(v.query, r())
                }

                function r() {
                    return f.test(v.query) ? void 0 : t
                }

                function d() {
                    return l.find(v.query + "." + g, r())
                }

                function p() {
                    var n = l.find(".fp-playlist", t)[0];
                    if (!n) {
                        n = l.createElement("div", {
                            className: "fp-playlist"
                        });
                        var r = l.find(".fp-next,.fp-prev", t);
                        r.length ? r[0].parentElement.insertBefore(n, r[0]) : l.insertAfter(t, l.find("video", t)[0], n)
                    }
                    n.innerHTML = "", e.conf.playlist[0].length && (e.conf.playlist = e.conf.playlist.map(function(e) {
                        if ("string" == typeof e) {
                            var t = e.split(s.TYPE_RE)[1];
                            return {
                                sources: [{
                                    type: "m3u8" === t.toLowerCase() ? "application/x-mpegurl" : "video/" + t,
                                    src: e
                                }]
                            }
                        }
                        return {
                            sources: e.map(function(e) {
                                var t = {};
                                return Object.keys(e).forEach(function(n) {
                                    t.type = /mpegurl/i.test(n) ? "application/x-mpegurl" : "video/" + n, t.src = e[n]
                                }), t
                            })
                        }
                    })), e.conf.playlist.forEach(function(e, t) {
                        var r = e.sources[0].src;
                        n.appendChild(l.createElement("a", {
                            href: r,
                            "data-index": t
                        }))
                    })
                }

                function m(t) {
                    return "undefined" != typeof t.index ? t.index : "undefined" != typeof e.video.index ? e.video.index : e.conf.startIndex || 0
                }
                var v = o({
                        active: "is-active",
                        advance: !0,
                        query: ".fp-playlist a"
                    }, e.conf),
                    g = v.active,
                    h = a(t);
                e.play = function(t) {
                    if (void 0 === t) return e.resume();
                    if ("number" == typeof t && !e.conf.playlist[t]) return e;
                    if ("number" != typeof t) return e.load.apply(null, arguments);
                    var n = o({
                        index: t
                    }, e.conf.playlist[t]);
                    return t === e.video.index ? e.load(n, function() {
                        e.resume()
                    }) : (e.off("resume.fromfirst"), e.load(n, function() {
                        e.video.index = t
                    }), e)
                }, e.next = function(t) {
                    t && t.preventDefault();
                    var n = e.video.index;
                    return -1 != n && (n = n === e.conf.playlist.length - 1 ? 0 : n + 1, e.play(n)), e
                }, e.prev = function(t) {
                    t && t.preventDefault();
                    var n = e.video.index;
                    return -1 != n && (n = 0 === n ? e.conf.playlist.length - 1 : n - 1, e.play(n)), e
                }, e.setPlaylist = function(t) {
                    return e.conf.playlist = t, delete e.video.index, p(), e
                }, e.addPlaylistItem = function(t) {
                    return e.setPlaylist(e.conf.playlist.concat([t]))
                }, e.removePlaylistItem = function(t) {
                    var n = e.conf.playlist;
                    return e.setPlaylist(n.slice(0, t).concat(n.slice(t + 1)))
                }, i.on(t, "click", ".fp-next", e.next), i.on(t, "click", ".fp-prev", e.prev), v.advance && e.off("finish.pl").on("finish.pl", function(e, t) {
                    if (t.video.loop) return t.seek(0, function() {
                        t.resume()
                    });
                    var n = t.video.index >= 0 ? t.video.index + 1 : void 0;
                    n < t.conf.playlist.length || v.loop ? (n = n === t.conf.playlist.length ? 0 : n, h.remove("is-finished"), setTimeout(function() {
                        t.play(n)
                    })) : t.conf.playlist.length > 1 && t.one("resume.fromfirst", function() {
                        return t.play(0), !1
                    })
                });
                var y = !1;
                e.conf.playlist.length && (y = !0, p(), e.conf.clip && e.conf.clip.sources.length || (e.conf.clip = e.conf.playlist[e.conf.startIndex || 0])), n().length && !y && (e.conf.playlist = [], delete e.conf.startIndex, n().forEach(function(t) {
                    var n = t.href;
                    t.setAttribute("data-index", e.conf.playlist.length);
                    var r = u.resolve(n, e.conf.clip.sources);
                    c && o(r, c(t).data()), e.conf.playlist.push(r)
                })), i.on(f.test(v.query) ? document : t, "click", v.query, function(t) {
                    t.preventDefault();
                    var n = t.currentTarget,
                        r = Number(n.getAttribute("data-index")); - 1 != r && e.play(r)
                }), e.on("load", function(n, o, i) {
                    if (e.conf.playlist.length) {
                        var s = d()[0],
                            u = s && s.getAttribute("data-index"),
                            c = i.index = m(i),
                            f = l.find(v.query + '[data-index="' + c + '"]', r())[0],
                            p = c == e.conf.playlist.length - 1;
                        s && a(s).remove(g), f && a(f).add(g), h.remove("video" + u), h.add("video" + c), l.toggleClass(t, "last-video", p), i.index = o.video.index = c, i.is_last = o.video.is_last = p
                    }
                }).on("unload.pl", function() {
                    e.conf.playlist.length && (d().forEach(function(e) {
                        a(e).toggle(g)
                    }), e.conf.playlist.forEach(function(e, t) {
                        h.remove("video" + t)
                    }))
                }), e.conf.playlist.length && (e.conf.loop = !1)
            })
        }, {
            "../common": 1,
            "../flowplayer": 18,
            "./resolve": 13,
            bean: 20,
            "class-list": 21,
            "extend-object": 25
        }],
        13: [function(e, t, n) {
            "use strict";

            function r(e) {
                var t = e.attr("src"),
                    n = e.attr("type") || "",
                    r = t.split(i)[1];
                return n = n.toLowerCase(), a(e.data(), {
                    src: t,
                    suffix: r || n,
                    type: n || r
                })
            }

            function o(e) {
                return /mpegurl/i.test(e) ? "application/x-mpegurl" : "video/" + e
            }
            var i = /\.(\w{3,4})(\?.*)?$/i,
                a = e("extend-object");
            t.exports = function() {
                var e = this;
                e.sourcesFromVideoTag = function(e, t) {
                    var n = [];
                    return t("source", e).each(function() {
                        n.push(r(t(this)))
                    }), !n.length && e.length && n.push(r(e)), n
                }, e.resolve = function(e, t) {
                    return e ? ("string" == typeof e && (e = {
                        src: e,
                        sources: []
                    }, e.sources = (t || []).map(function(t) {
                        var n = t.src.split(i)[1];
                        return {
                            type: t.type,
                            src: e.src.replace(i, "." + n + "$2")
                        }
                    })), e instanceof Array && (e = {
                        sources: e.map(function(e) {
                            return e.type && e.src ? e : Object.keys(e).reduce(function(t, n) {
                                return a(t, {
                                    type: o(n),
                                    src: e[n]
                                })
                            }, {})
                        })
                    }), e) : {
                        sources: t
                    }
                }
            }, t.exports.TYPE_RE = i
        }, {
            "extend-object": 25
        }],
        14: [function(e, t, n) {
            "use strict";
            var r = e("class-list"),
                o = e("bean"),
                i = e("../common"),
                a = function(e, t) {
                    var n;
                    return function() {
                        n || (e.apply(this, arguments), n = 1, setTimeout(function() {
                            n = 0
                        }, t))
                    }
                },
                l = function(e, t) {
                    var n, l, s, u, c, f, d, p, m = (/iPad/.test(navigator.userAgent) && !/CriOS/.test(navigator.userAgent), i.lastChild(e)),
                        v = r(e),
                        g = r(m),
                        h = !1,
                        y = function() {
                            l = i.offset(e), s = i.width(e), u = i.height(e), f = c ? u : s, p = E(d)
                        },
                        b = function(t) {
                            n || t == k.value || d && !(d > t) || (o.fire(e, "slide", [t]), k.value = t)
                        },
                        w = function(e) {
                            var n = e.pageX || e.clientX;
                            !n && e.originalEvent && e.originalEvent.touches && e.originalEvent.touches.length && (n = e.originalEvent.touches[0].pageX);
                            var r = c ? e.pageY - l.top : n - l.left;
                            r = Math.max(0, Math.min(p || f, r));
                            var o = r / f;
                            return c && (o = 1 - o), t && (o = 1 - o), x(o, 0, !0)
                        },
                        x = function(e, t) {
                            void 0 === t && (t = 0), e > 1 && (e = 1);
                            var n = Math.round(1e3 * e) / 10 + "%";
                            return (!d || d >= e) && (g.remove("animated"), h ? g.remove("animated") : (g.add("animated"), i.css(m, "transition-duration", (t || 0) + "ms")), i.css(m, "width", n)), e
                        },
                        E = function(e) {
                            return Math.max(0, Math.min(f, c ? (1 - e) * u : e * s))
                        },
                        k = {
                            max: function(e) {
                                d = e
                            },
                            disable: function(e) {
                                n = e
                            },
                            slide: function(e, t, n) {
                                y(), n && b(e), x(e, t)
                            },
                            disableAnimation: function(t, n) {
                                h = t !== !1, i.toggleClass(e, "no-animation", !!n)
                            }
                        };
                    return y(), o.on(e, "mousedown.sld touchstart", function(e) {
                        if (e.preventDefault(), !n) {
                            var t = a(b, 100);
                            y(), k.dragging = !0, v.add("is-dragging"), b(w(e)), o.on(document, "mousemove.sld touchmove.sld", function(e) {
                                e.preventDefault(), t(w(e))
                            }), o.one(document, "mouseup touchend", function() {
                                k.dragging = !1, v.remove("is-dragging"), o.off(document, "mousemove.sld touchmove.sld")
                            })
                        }
                    }), k
                };
            t.exports = l
        }, {
            "../common": 1,
            bean: 20,
            "class-list": 21
        }],
        15: [function(e, t, n) {
            "use strict";
            var r = e("../flowplayer"),
                o = e("../common"),
                i = e("bean"),
                a = e("class-list");
            r.defaults.subtitleParser = function(e) {
                function t(e) {
                    var t = e.split(":");
                    return 2 == t.length && t.unshift(0), 60 * t[0] * 60 + 60 * t[1] + parseFloat(t[2].replace(",", "."))
                }
                for (var n, r, o, i = /^(([0-9]{2}:){1,2}[0-9]{2}[,.][0-9]{3}) --\> (([0-9]{2}:){1,2}[0-9]{2}[,.][0-9]{3})(.*)/, a = [], l = 0, s = e.split("\n"), u = s.length, c = {}; u > l; l++)
                    if (r = i.exec(s[l])) {
                        for (n = s[l - 1], o = "<p>" + s[++l] + "</p><br/>";
                            "string" == typeof s[++l] && s[l].trim() && l < s.length;) o += "<p>" + s[l] + "</p><br/>";
                        c = {
                            title: n,
                            startTime: t(r[1]),
                            endTime: t(r[3]),
                            text: o
                        }, a.push(c)
                    } return a
            }, r(function(e, t) {
                var n, l, s, u, c = a(t),
                    f = function() {
                        u = o.createElement("a", {
                            className: "fp-menu"
                        });
                        var n = o.createElement("ul", {
                            className: "fp-dropdown fp-dropup"
                        });
                        return n.appendChild(o.createElement("li", {
                            "data-subtitle-index": -1
                        }, "No subtitles")), (e.video.subtitles || []).forEach(function(e, t) {
                            var r = e.srclang || "en",
                                i = e.label || "Default (" + r + ")",
                                a = o.createElement("li", {
                                    "data-subtitle-index": t
                                }, i);
                            n.appendChild(a)
                        }), u.appendChild(n), o.find(".fp-controls", t)[0].appendChild(u), u
                    };
                i.on(t, "click", ".fp-menu", function(e) {
                    a(u).toggle("dropdown-open")
                }), i.on(t, "click", ".fp-menu li[data-subtitle-index]", function(t) {
                    var n = t.target.getAttribute("data-subtitle-index");
                    return "-1" === n ? e.disableSubtitles() : void e.loadSubtitles(n)
                });
                var d = function() {
                    var e = o.find(".fp-player", t)[0];
                    s = o.find(".fp-subtitle", t)[0], s = s || o.appendTo(o.createElement("div", {
                        "class": "fp-subtitle"
                    }), e), Array.prototype.forEach.call(s.children, o.removeNode), n = a(s), o.find(".fp-menu", t).forEach(o.removeNode), f()
                };
                e.on("ready", function(n, i, a) {
                    var l = i.conf;
                    if (r.support.subtitles && l.nativesubtitles && "html5" == i.engine.engineName) {
                        var s = function(e) {
                            var n = o.find("video", t)[0].textTracks;
                            n.length && (n[0].mode = e)
                        };
                        if (!a.subtitles || !a.subtitles.length) return;
                        var u = o.find("video.fp-engine", t)[0];
                        return a.subtitles.some(function(e) {
                            return !o.isSameDomain(e.src)
                        }) && o.attr(u, "crossorigin", "anonymous"), u.textTracks.addEventListener("addtrack", function() {
                            s("disabled"), s("showing")
                        }), void a.subtitles.forEach(function(e) {
                            u.appendChild(o.createElement("track", {
                                kind: "subtitles",
                                srclang: e.srclang || "en",
                                label: e.label || "en",
                                src: e.src,
                                "default": e["default"]
                            }))
                        })
                    }
                    if (i.subtitles = [], d(), c.remove("has-menu"), e.disableSubtitles(), a.subtitles && a.subtitles.length) {
                        c.add("has-menu");
                        var f = a.subtitles.filter(function(e) {
                            return e["default"]
                        })[0];
                        f && i.loadSubtitles(a.subtitles.indexOf(f))
                    }
                }), e.bind("cuepoint", function(e, t, r) {
                    r.subtitle ? (l = r.index, o.html(s, r.subtitle.text), n.add("fp-active")) : r.subtitleEnd && (n.remove("fp-active"), l = r.index)
                }), e.bind("seek", function(t, r, o) {
                    l && e.cuepoints[l] && e.cuepoints[l].time > o && (n.remove("fp-active"), l = null), (e.cuepoints || []).forEach(function(t) {
                        var n = t.subtitle;
                        n && l != t.index ? o >= t.time && (!n.endTime || o <= n.endTime) && e.trigger("cuepoint", [e, t]) : t.subtitleEnd && o >= t.time && t.index == l + 1 && e.trigger("cuepoint", [e, t])
                    })
                });
                var p = function(e) {
                    o.toggleClass(o.find("li.active", t)[0], "active"), o.toggleClass(o.find('li[data-subtitle-index="' + e + '"]', t)[0], "active")
                };
                e.disableSubtitles = function() {
                    return e.subtitles = [], (e.cuepoints || []).forEach(function(t) {
                        (t.subtitle || t.subtitleEnd) && e.removeCuepoint(t)
                    }), s && Array.prototype.forEach.call(s.children, o.removeNode), p(-1), e
                }, e.loadSubtitles = function(t) {
                    e.disableSubtitles();
                    var n = e.video.subtitles[t],
                        r = n.src;
                    return r ? (p(t), o.xhrGet(r, function(t) {
                        var n = e.conf.subtitleParser(t);
                        n.forEach(function(t) {
                            var n = {
                                time: t.startTime,
                                subtitle: t,
                                visible: !1
                            };
                            e.subtitles.push(t), e.addCuepoint(n), e.addCuepoint({
                                time: t.endTime,
                                subtitleEnd: t.title,
                                visible: !1
                            }), 0 !== t.startTime || e.video.time || e.trigger("cuepoint", [e, n])
                        })
                    }, function() {
                        return e.trigger("error", {
                            code: 8,
                            url: r
                        }), !1
                    }), e) : void 0
                }
            })
        }, {
            "../common": 1,
            "../flowplayer": 18,
            bean: 20,
            "class-list": 21
        }],
        16: [function(e, t, n) {
            "use strict";
            var r = e("../flowplayer"),
                o = e("extend-object");
            ! function() {
                var e = function(e) {
                        var t = /Version\/(\d\.\d)/.exec(e);
                        return t && t.length > 1 ? parseFloat(t[1], 10) : 0
                    },
                    t = function() {
                        var e = document.createElement("video");
                        return e.loop = !0, e.autoplay = !0, e.preload = !0, e
                    },
                    n = {},
                    i = navigator.userAgent.toLowerCase(),
                    a = /(chrome)[ \/]([\w.]+)/.exec(i) || /(safari)[ \/]([\w.]+)/.exec(i) || /(webkit)[ \/]([\w.]+)/.exec(i) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(i) || /(msie) ([\w.]+)/.exec(i) || i.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(i) || [];
                a[1] && (n[a[1]] = !0, n.version = a[2] || "0");
                var l = t(),
                    s = navigator.userAgent,
                    u = n.msie || /Trident\/7/.test(s),
                    c = /iPad|MeeGo/.test(s) && !/CriOS/.test(s),
                    f = /iPad/.test(s) && /CriOS/.test(s),
                    d = /iP(hone|od)/i.test(s) && !/iPad/.test(s) && !/IEMobile/i.test(s),
                    p = /Android/.test(s) && !/Firefox/.test(s),
                    m = /Android/.test(s) && /Firefox/.test(s),
                    v = /Silk/.test(s),
                    g = /IEMobile/.test(s),
                    h = g ? parseFloat(/Windows\ Phone\ (\d+\.\d+)/.exec(s)[1], 10) : 0,
                    y = g ? parseFloat(/IEMobile\/(\d+\.\d+)/.exec(s)[1], 10) : 0,
                    b = (c ? e(s) : 0, p ? parseFloat(/Android\ (\d+(\.\d+)?)/.exec(s)[1], 10) : 0),
                    w = o(r.support, {
                        browser: n,
                        subtitles: !!l.addTextTrack,
                        fullscreen: "function" == typeof document.webkitCancelFullScreen && !/Mac OS X 10_5.+Version\/5\.0\.\d Safari/.test(s) || document.mozFullScreenEnabled || "function" == typeof document.exitFullscreen || "function" == typeof document.msExitFullscreen,
                        inlineBlock: !(u && n.version < 8),
                        touch: "ontouchstart" in window,
                        dataload: !c && !d && !g,
                        zeropreload: !u && !p,
                        volume: !(c || p || d || v || f || m),
                        cachedVideoTag: !(c || d || f || g),
                        firstframe: !(p || v || g || m),
                        autoplay: !(c || d || f || p || v || g || m),
                        inlineVideo: (!g || h >= 8.1 && y >= 11) && (!p || b >= 3),
                        hlsDuration: !p && (!n.safari || c || d || f),
                        seekable: !c && !f,
                        iphone: d,
                        ios: d || c || f
                    });
                try {
                    var x = navigator.plugins["Shockwave Flash"],
                        E = u ? new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version") : x.description;
                    u || x[0].enabledPlugin ? (E = E.split(/\D+/), E.length && !E[0] && (E = E.slice(1)), w.flashVideo = E[0] > 9 || 9 == E[0] && E[3] >= 115) : w.flashVideo = !1
                } catch (k) {}
                try {
                    w.video = !!l.canPlayType, w.video && l.canPlayType("video/mp4")
                } catch (T) {
                    w.video = !1
                }
                w.animation = function() {
                    for (var e = ["", "Webkit", "Moz", "O", "ms", "Khtml"], t = document.createElement("p"), n = 0; n < e.length; n++)
                        if ("undefined" != typeof t.style[e[n] + "AnimationName"]) return !0
                }()
            }()
        }, {
            "../flowplayer": 18,
            "extend-object": 25
        }],
        17: [function(e, t, n) {
            "use strict";

            function r(e) {
                return e = parseInt(e, 10), e >= 10 ? e : "0" + e
            }

            function o(e) {
                e = e || 0;
                var t = Math.floor(e / 3600),
                    n = Math.floor(e / 60);
                return e -= 60 * n, t >= 1 ? (n -= 60 * t, t + ":" + r(n) + ":" + r(e)) : r(n) + ":" + r(e)
            }
            var i = e("../flowplayer"),
                a = e("../common"),
                l = e("class-list"),
                s = e("bean"),
                u = e("./slider");
            i(function(e, t) {
                function n(e) {
                    return a.find(".fp-" + e, t)[0]
                }

                function r(e) {
                    a.css(w, "padding-top", 100 * e + "%"), p.inlineBlock || a.height(a.find("object", t)[0], a.height(t))
                }

                function c(e) {
                    e ? (m.add("is-mouseover"), m.remove("is-mouseout")) : (m.add("is-mouseout"), m.remove("is-mouseover"))
                }
                var f, d = e.conf,
                    p = i.support,
                    m = l(t);
                a.find(".fp-ratio,.fp-ui", t).forEach(a.removeNode), m.add("flowplayer"), t.appendChild(a.createElement("div", {
                    className: "fp-ratio"
                }));
                var v = a.createElement("div", {
                    className: "fp-ui"
                }, '         <div class="waiting"><em></em><em></em><em></em></div>         <a class="fullscreen"></a>         <a class="unload"></a>         <p class="speed"></p>         <div class="controls">            <a class="play"></a>            <div class="timeline">               <div class="buffer"></div>               <div class="progress"></div>            </div>            <div class="timeline-tooltip fp-tooltip"></div>            <div class="volume">               <a class="mute"></a>               <div class="volumeslider">                  <div class="volumelevel"></div>               </div>            </div>         </div>         <div class="time">            <em class="elapsed">00:00</em>            <em class="remaining"></em>            <em class="duration">00:00</em>         </div>         <div class="message"><h2></h2><p></p></div>'.replace(/class="/g, 'class="fp-'));
                t.appendChild(v);
                var g = (n("progress"), n("buffer")),
                    h = n("elapsed"),
                    y = n("remaining"),
                    b = n("waiting"),
                    w = n("ratio"),
                    x = n("speed"),
                    E = l(x),
                    k = n("duration"),
                    T = n("controls"),
                    S = n("timeline-tooltip"),
                    N = a.css(w, "padding-top"),
                    C = n("timeline"),
                    j = u(C, e.rtl),
                    O = (n("volume"), n("fullscreen")),
                    P = n("volumeslider"),
                    A = u(P, e.rtl),
                    _ = m.contains("fixed-controls") || m.contains("no-toggle");
                j.disableAnimation(m.contains("is-touch")), e.sliders = e.sliders || {}, e.sliders.timeline = j, e.sliders.volume = A, p.animation || a.html(b, "<p>loading &hellip;</p>"), d.ratio && r(d.ratio);
                try {
                    d.fullscreen || a.removeNode(O)
                } catch (D) {
                    a.removeNode(O)
                }
                e.on("ready", function(e, n, i) {
                    var l = n.video.duration;
                    j.disable(n.disabled || !l), d.adaptiveRatio && !isNaN(i.height / i.width) && r(i.height / i.width, !0), a.html([k, y], o(l)), a.toggleClass(t, "is-long", l >= 3600), A.slide(n.volumeLevel), "flash" === n.engine.engineName ? j.disableAnimation(!0, !0) : j.disableAnimation(!1), a.find(".fp-title", v).forEach(a.removeNode), i.title && a.prepend(v, a.createElement("div", {
                        className: "fp-title"
                    }, i.title))
                }).on("unload", function() {
                    N || d.splash || a.css(w, "paddingTop", ""), j.slide(0)
                }).on("buffer", function(xE, xA, xV) {
                    var t = e.video,
                        n = Math.round(xV) / Math.round(t.duration);
                    if (typeof xV != 'undefined') {
                        !t.seekable && p.seekable && j.max(n), 1 > n ? a.css(g, "width", 100 * n + "%") : a.css(g, "width", "100%")
                    }
                }).on("speed", function(e, t, n) {
                    a.text(x, n + "x"), E.add("fp-hilite"), setTimeout(function() {
                        E.remove("fp-hilite")
                    }, 1e3)
                }).on("buffered", function() {
                    a.css(g, "width", "100%"), j.max(1)
                }).on("progress", function() {
                    var t = e.video.time,
                        n = e.video.duration;
                    j.dragging || j.slide(t / n, e.seeking ? 0 : 250), a.html(h, o(t)), a.html(y, "-" + o(n - t))
                }).on("finish resume seek", function(e) {
                    a.toggleClass(t, "is-finished", "finish" == e.type)
                }).on("stop", function() {
                    a.html(h, o(0)), j.slide(0, 100)
                }).on("finish", function() {
                    a.html(h, o(e.video.duration)), j.slide(1, 100), m.remove("is-seeking")
                }).on("beforeseek", function() {}).on("volume", function() {
                    A.slide(e.volumeLevel)
                }).on("disable", function() {
                    var n = e.disabled;
                    j.disable(n), A.disable(n), a.toggleClass(t, "is-disabled", e.disabled)
                }).on("mute", function(e, n, r) {
                    a.toggleClass(t, "is-muted", r)
                }).on("error", function(e, n, r) {
                    if (a.removeClass(t, "is-loading"), a.removeClass(t, "is-seeking"), a.addClass(t, "is-error"), r) {
                        var errorMessage = d.errors[r.code];
                        n.error = !0;
                        var o = a.find(".fp-message", t)[0],
                            i = r.video || n.video;
                        a.find("h2", o)[0].innerHTML = (n.engine && n.engine.engineName || "html5") + ": " + errorMessage, a.find("p", o)[0].innerHTML = r.url || i.url || i.src || d.errorUrls[r.code], n.off("mouseenter click"), m.remove("is-mouseover")
                    }
                }), s.on(t, "mouseenter mouseleave", function(n) {
                    if (!_) {
                        var r, o = "mouseover" == n.type;
                        if (c(o), o) {
                            var i = function() {
                                c(!0), r = new Date
                            };
                            e.on("pause.x volume.x", i), s.on(t, "mousemove.x", i), s.on(t, "touchmove.x", i), f = setInterval(function() {
                                new Date - r > d.mouseoutTimeout && (c(!1), r = new Date)
                            }, 100)
                        } else s.off(t, "mousemove.x"), s.off(t, "touchmove.x"), e.off("pause.x volume.x"), clearInterval(f)
                    }
                }), s.on(t, "mouseleave", function() {
                    (j.dragging || A.dragging) && (m.add("is-mouseover"), m.remove("is-mouseout"))
                }), s.on(t, "click.player", function(t) {
                    if (!e.disabled) {
                        var n = l(t.target);
                        return n.contains("fp-ui") || n.contains("fp-engine") || t.flash ? (t.preventDefault && t.preventDefault(), e.toggle()) : void 0
                    }
                }), s.on(t, "mousemove touchmove touchstart", ".fp-timeline", function(ev) {
                    var xX = ev.pageX || ev.clientX || (ev.changedTouches ? ev.changedTouches[0].clientX : 0),
                        r = xX - a.offset(C).left,
                        i = r / a.width(C),
                        l = i * e.video.duration;
                    0 > i || (a.html(S, '<span class="fp-timeline-time">' + o(l) + '</span>'), a.css(S, "left", Math.max(2, Math.min(a.width(n('player')) - a.width(S) - 2, xX - a.offset(T).left - a.width(S) / 2)) + "px"))
                }), s.on(t, "contextmenu", function(e) {
                    var n = a.offset(a.find(".fp-player", t)[0]),
                        r = window,
                        o = e.clientX - (n.left + r.scrollX),
                        i = e.clientY - (n.top + r.scrollY);
                    if (!m.contains("is-flash-disabled")) {
                        var l = a.find(".fp-context-menu", t)[0];
                        l && (e.preventDefault(), a.css(l, {
                            left: o + "px",
                            top: i + "px",
                            display: "block"
                        }), s.on(t, "click", ".fp-context-menu", function(e) {
                            e.stopPropagation()
                        }), s.on(document, "click.outsidemenu", function(e) {
                            a.css(l, "display", "none"), s.off(document, "click.outsidemenu")
                        }))
                    }
                }), e.on("flashdisabled", function() {
                    m.add("is-flash-disabled"), e.one("ready progress", function() {
                        m.remove("is-flash-disabled"), a.find(".fp-flash-disabled", t).forEach(a.removeNode)
                    }), t.appendChild(a.createElement("div", {
                        className: "fp-flash-disabled"
                    }, "Adobe Flash is disabled for this page, click player area to enable"))
                }), d.poster && a.css(t, "background-image", "url(" + d.poster + ")");
                var M = a.css(t, "background-color"),
                    L = "none" != a.css(t, "background-image") || M && "rgba(0, 0, 0, 0)" != M && "transparent" != M;
                L && !d.splash && (d.poster || (d.poster = !0), e.on("ready stop", function() {
                    m.add("is-poster"), e.poster = !0, e.one("progress", function() {
                        m.remove("is-poster"), e.poster = !1
                    })
                })), "string" == typeof d.splash && a.css(t, "background-image", "url('" + d.splash + "')"), !L && e.forcedSplash && a.css(t, "background-color", "#555"), s.on(t, "click", ".fp-toggle, .fp-play", function() {
                    e.disabled || e.toggle()
                }), s.on(t, "click", ".fp-mute", function() {
                    e.mute()
                }), s.on(t, "click", ".fp-fullscreen", function() {
                    e.fullscreen()
                }), s.on(t, "click", ".fp-unload", function() {
                    e.unload()
                }), s.on(C, "slide", function(t) {
                    e.seeking = !0, e.seek(t * e.video.duration)
                }), s.on(P, "slide", function(t) {
                    e.volume(t)
                });
                var I = n("time");
                s.on(t, "click", ".fp-time", function() {
                    l(I).toggle("is-inverted")
                }), c(_), e.on("shutdown", function() {
                    s.off(C), s.off(P)
                })
            }), t.exports.format = o
        }, {
            "../common": 1,
            "../flowplayer": 18,
            "./slider": 14,
            bean: 20,
            "class-list": 21
        }],
        18: [function(e, t, n) {
            "use strict";

            function r(e, t, n) {
                t && t.embed && (t.embed = o({}, y.defaults.embed, t.embed));
                var r, d, m = e,
                    v = a(m),
                    g = o({}, y.defaults, y.conf, t),
                    h = {},
                    x = new w;
                v.add("is-loading");
                try {
                    h = p ? window.localStorage : h
                } catch (E) {}
                var k = m.currentStyle && "rtl" === m.currentStyle.direction || window.getComputedStyle && null !== window.getComputedStyle(m, null) && "rtl" === window.getComputedStyle(m, null).getPropertyValue("direction");
                k && v.add("is-rtl");
                var T = {
                    conf: g,
                    currentSpeed: 1,
                    volumeLevel: g.muted ? 0 : "undefined" == typeof g.volume ? 1 * h.volume : g.volume,
                    video: {},
                    disabled: !1,
                    finished: !1,
                    loading: !1,
                    muted: "true" == h.muted || g.muted,
                    paused: !1,
                    playing: !1,
                    ready: !1,
                    splash: !1,
                    rtl: k,
                    load: function(e, t) {
                        if (!T.error && !T.loading) {
                            T.video = {}, T.finished = !1, e = e || g.clip, e = o({}, x.resolve(e, g.clip.sources)), (T.playing || T.engine) && (e.autoplay = !0);
                            var n = S(e);
                            if (!n) return T.trigger("error", [T, {
                                code: y.support.flashVideo ? 5 : 10
                            }]);
                            if (!n.engineName) throw new Error("engineName property of factory should be exposed");
                            if (T.engine && n.engineName === T.engine.engineName || (T.ready = !1, T.engine && (T.engine.unload(), T.conf.autoplay = !0), d = T.engine = n(T, m), T.one("ready", function() {
                                    d.volume(T.volumeLevel)
                                })), o(e, d.pick(e.sources.filter(function(e) {
                                    return e.engine ? e.engine === d.engineName : !0
                                }))), e.src) {
                                var r = T.trigger("load", [T, e, d], !0);
                                r.defaultPrevented ? T.loading = !1 : (d.load(e), i(e) && (t = e), t && T.one("ready", t))
                            }
                            return T
                        }
                    },
                    pause: function(e) {
                        return !T.ready || T.seeking || T.loading || (d.pause(), T.one("pause", e)), T
                    },
                    resume: function() {
                        return T.ready && T.paused && (d.resume(), T.finished && (T.trigger("resume", [T]), T.finished = !1)), T
                    },
                    toggle: function() {
                        return T.ready ? T.paused ? T.resume() : T.pause() : T.load()
                    },
                    seek: function(e, t) {
                        if (T.ready && !T.live) {
                            if ("boolean" == typeof e) {
                                var n = .1 * T.video.duration;
                                e = T.video.time + (e ? n : -n)
                            }
                            e = r = Math.min(Math.max(e, 0), T.video.duration - .1).toFixed(1);
                            var o = T.trigger("beforeseek", [T, e], !0);
                            o.defaultPrevented ? (T.seeking = !1, s.toggleClass(m, "is-seeking", T.seeking)) : (d.seek(e), i(t) && T.one("seek", t))
                        }
                        return T
                    },
                    seekTo: function(e, t) {
                        var n = void 0 === e ? r : .1 * T.video.duration * e;
                        return T.seek(n, t)
                    },
                    mute: function(e, t) {
                        return void 0 === e && (e = !T.muted), t || (h.muted = T.muted = e, h.volume = isNaN(h.volume) ? g.volume : h.volume), T.volume(e ? 0 : h.volume, !0), T.trigger("mute", [T, e]), T
                    },
                    volume: function(e, t) {
                        return e = Math.min(Math.max(e, 0), 1), T.volumeLevel = e, T.trigger("volume", [T, e]), t || (h.volume = e), T.ready && d.volume(e), T
                    },
                    speed: function(e, t) {
                        return T.ready && ("boolean" == typeof e && (e = g.speeds[g.speeds.indexOf(T.currentSpeed) + (e ? 1 : -1)] || T.currentSpeed), d.speed(e), t && m.one("speed", t)), T
                    },
                    stop: function() {
                        return T.ready && (T.pause(), T.seek(0, function() {
                            T.trigger("stop", [T])
                        })), T
                    },
                    unload: function() {
                        return v.contains("is-embedding") || (g.splash ? (T.trigger("unload", [T]), d && (d.unload(), T.engine = d = 0)) : T.stop()), T
                    },
                    shutdown: function() {
                        T.unload(), T.trigger("shutdown", [T]), l.off(m), delete c[m.getAttribute("data-flowplayer-instance-id")], m.removeAttribute("data-flowplayer-instance-id")
                    },
                    disable: function(e) {
                        return void 0 === e && (e = !T.disabled), e != T.disabled && (T.disabled = e, T.trigger("disable", e)), T
                    }
                };
                T.conf = o(T.conf, g), u(T);
                T.time = Math.round(new Date().getTime() / 1000);
                var S = function(e) {
                    var t, n = y.engines;
                    if (g.engine) {
                        var r = n.filter(function(e) {
                            return e.engineName === g.engine
                        })[0];
                        if (r && e.sources.some(function(e) {
                                return e.engine && e.engine !== r.engineName ? !1 : r.canPlay(e.type, T.conf)
                            })) return r
                    }
                    return g.enginePreference && (n = y.engines.filter(function(e) {
                        return g.enginePreference.indexOf(e.engineName) > -1
                    }).sort(function(e, t) {
                        return g.enginePreference.indexOf(e.engineName) - g.enginePreference.indexOf(t.engineName)
                    })), e.sources.some(function(e) {
                        var r = n.filter(function(t) {
                            return e.engine && e.engine !== t.engineName ? !1 : t.canPlay(e.type, T.conf)
                        }).shift();
                        return r && (t = r), !!r
                    }), t
                };
                return m.getAttribute("data-flowplayer-instance-id") || (m.setAttribute("data-flowplayer-instance-id", b++), T.on("boot", function() {
                    (g.splash || v.contains("is-splash") || !y.support.firstframe) && (T.forcedSplash = !g.splash && !v.contains("is-splash"), T.splash = g.autoplay = !0, g.splash || (g.splash = !0), v.add("is-splash")), g.splash && s.find("video", m).forEach(s.removeNode), (g.live || v.contains("is-live")) && (T.live = g.live = !0, v.add("is-live")), f.forEach(function(e) {
                        e(T, m)
                    }), c.push(T), g.splash ? T.unload() : T.load(), g.disabled && T.disable(), T.one("ready", n)
                }).on("load", function(e, t, n) {
                    g.splash && s.find(".flowplayer.is-ready,.flowplayer.is-loading").forEach(function(e) {
                        var t = e.getAttribute("data-flowplayer-instance-id");
                        if (t !== m.getAttribute("data-flowplayer-instance-id")) {
                            var n = c[Number(t)];
                            n && n.conf.splash && n.unload()
                        }
                    }), v.add("is-loading"), t.loading = !0, "undefined" != typeof n.live && (s.toggleClass(m, "is-live", n.live), t.live = n.live)
                }).on("ready", function(e, t, n) {
                    n.time = 0, t.video = n, v.remove("is-loading"), t.loading = !1, t.muted ? t.mute(!0, !0) : t.volume(t.volumeLevel);
                    var r = t.conf.hlsFix && /mpegurl/i.exec(n.type);
                    s.toggleClass(m, "hls-fix", !!r)
                }).on("unload", function(e) {
                    v.remove("is-loading"), T.loading = !1
                }).on("ready unload", function(e) {
                    var t = "ready" == e.type;
                    s.toggleClass(m, "is-splash", !t), s.toggleClass(m, "is-ready", t), T.ready = t, T.splash = !t
                }).on("progress", function(e, t, n) {
                    t.video.time = n
                }).on("speed", function(e, t, n) {
                    t.currentSpeed = n
                }).on("volume", function(e, t, n) {
                    t.volumeLevel = Math.round(100 * n) / 100, t.muted ? n && !y.support.ios && t.mute(!1) : h.volume = n
                }).on("beforeseek seek", function(e) {
                    T.seeking = "beforeseek" == e.type, s.toggleClass(m, "is-seeking", T.seeking)
                }).on("ready pause resume unload finish stop", function(e, t, n) {
                    T.paused = /pause|finish|unload|stop/.test(e.type), T.paused = T.paused || "ready" === e.type && !g.autoplay && !T.playing, T.playing = !T.paused, s.toggleClass(m, "is-paused", T.paused), s.toggleClass(m, "is-playing", T.playing), T.load.ed || T.pause()
                }).on("finish", function(e) {
                    T.finished = !0
                }).on("error", function() {})), T.trigger("boot", [T, m]), T
            }
            var o = e("extend-object"),
                i = e("is-function"),
                a = e("class-list"),
                l = e("bean"),
                s = e("./common"),
                u = e("./ext/events"),
                c = [],
                f = [],
                d = (window.navigator.userAgent, window.onbeforeunload);
            window.onbeforeunload = function(e) {
                return c.forEach(function(e) {
                    e.conf.splash ? e.unload() : e.bind("error", function() {
                        s.find(".flowplayer.is-error .fp-message").forEach(s.removeNode)
                    })
                }), d ? d(e) : void 0
            };
            var p = !1;
            try {
                "object" == typeof window.localStorage && (window.localStorage.flowplayerTestStorage = "test", p = !0)
            } catch (m) {}
            var v = /Safari/.exec(navigator.userAgent) && !/Chrome/.exec(navigator.userAgent),
                g = /(\d+\.\d+) Safari/.exec(navigator.userAgent),
                h = g ? Number(g[1]) : 100,
                y = t.exports = function(e, t, n) {
                    if (i(e)) return f.push(e);
                    if ("number" == typeof e || "undefined" == typeof e) return c[e || 0];
                    if (e.nodeType) {
                        if (null !== e.getAttribute("data-flowplayer-instance-id")) return c[e.getAttribute("data-flowplayer-instance-id")];
                        if (!t) return;
                        return r(e, t, n)
                    }
                    if (e.jquery) return y(e[0], t, n);
                    if ("string" == typeof e) {
                        var o = s.find(e)[0];
                        return o && y(o, t, n)
                    }
                };
            o(y, {
                version: "6.0.5",
                engines: [],
                conf: {},
                set: function(e, t) {
                    "string" == typeof e ? y.conf[e] = t : o(y.conf, e)
                },
                support: {},
                defaults: {
                    debug: p ? !!localStorage.flowplayerDebug : !1,
                    disabled: !1,
                    fullscreen: window == window.top,
                    keyboard: !0,
                    ratio: 9 / 16,
                    adaptiveRatio: !1,
                    rtmp: 0,
                    proxy: "best",
                    splash: !1,
                    live: !1,
                    swf: "//releases.flowplayer.org/6.0.5/commercial/flowplayer.swf",
                    swfHls: "//releases.flowplayer.org/6.0.5/commercial/flowplayerhls.swf",
                    speeds: [.25, .5, 1, 1.5, 2],
                    tooltip: !0,
                    mouseoutTimeout: 5e3,
                    volume: p ? "true" == localStorage.muted ? 0 : isNaN(localStorage.volume) ? 1 : localStorage.volume || 1 : 1,
                    errors: ["", "Video loading aborted", "Network error", "Video not properly encoded", "Video file not found", "Unsupported video", "Skin not found", "SWF file not found", "Subtitles not found", "Invalid RTMP URL", "Unsupported video format. Try installing Adobe Flash."],
                    errorUrls: ["", "", "", "", "", "", "", "", "", "", "http://get.adobe.com/flashplayer/"],
                    playlist: [],
                    hlsFix: v && 8 > h
                },
                bean: l,
                common: s,
                extend: o
            });
            var b = 0,
                w = e("./ext/resolve");
            if ("undefined" != typeof window.jQuery) {
                var x = window.jQuery;
                x(function() {
                    "function" == typeof x.fn.flowplayer && x('.flowplayer:has(video,script[type="application/json"])').flowplayer()
                });
                var E = function(e) {
                    if (!e.length) return {};
                    var t = e.data() || {},
                        n = {};
                    return x.each(["autoplay", "loop", "preload", "poster"], function(r, o) {
                        var i = e.attr(o);
                        void 0 !== i && -1 !== ["autoplay", "poster"].indexOf(o) ? n[o] = i ? i : !0 : void 0 !== i && (t[o] = i ? i : !0)
                    }), t.subtitles = e.find("track").map(function() {
                        var e = x(this);
                        return {
                            src: e.attr("src"),
                            kind: e.attr("kind"),
                            label: e.attr("label"),
                            srclang: e.attr("srclang"),
                            "default": e.prop("default")
                        }
                    }).get(), t.sources = (new w).sourcesFromVideoTag(e, x), o(n, {
                        clip: t
                    })
                };
                x.fn.flowplayer = function(e, t) {
                    return this.each(function() {
                        "string" == typeof e && (e = {
                            swf: e
                        }), i(e) && (t = e, e = {});
                        var n = x(this),
                            o = n.find('script[type="application/json"]'),
                            a = o.length ? JSON.parse(o.text()) : E(n.find("video")),
                            l = x.extend({}, e || {}, a, n.data()),
                            s = r(this, l, t);
                        u.EVENTS.forEach(function(e) {
                            s.on(e + ".jquery", function(e) {
                                n.trigger.call(n, e.type, e.detail && e.detail.args)
                            })
                        }), n.data("flowplayer", s)
                    })
                }
            }
        }, {
            "./common": 1,
            "./ext/events": 8,
            "./ext/resolve": 13,
            bean: 20,
            "class-list": 21,
            "extend-object": 25,
            "is-function": 26
        }],
        19: [function(e, t, n) {
            e("es5-shim");
            var r = t.exports = e("./flowplayer");
            e("./ext/support"), e("./engine/embed"), e("./engine/html5"), e("./engine/flash"), e("./ext/ui"), e("./ext/keyboard"), e("./ext/playlist"), e("./ext/cuepoint"), e("./ext/subtitle"), e("./ext/analytics"), e("./ext/embed"), e("./ext/fullscreen"), e("./ext/mobile"), r(function(e, t) {
                function n(e) {
                    var t = document.createElement("a");
                    return t.href = e, u.hostname(t.hostname)
                }

                function o(e) {
                    var t = "ab.ca,ac.ac,ac.at,ac.be,ac.cn,ac.il,ac.in,ac.jp,ac.kr,ac.sg,ac.th,ac.uk,ad.jp,adm.br,adv.br,ah.cn,am.br,arq.br,art.br,arts.ro,asn.au,asso.fr,asso.mc,bc.ca,bio.br,biz.pl,biz.tr,bj.cn,br.com,cn.com,cng.br,cnt.br,co.ac,co.at,co.de,co.gl,co.hk,co.id,co.il,co.in,co.jp,co.kr,co.mg,co.ms,co.nz,co.th,co.uk,co.ve,co.vi,co.za,com.ag,com.ai,com.ar,com.au,com.br,com.cn,com.co,com.cy,com.de,com.do,com.ec,com.es,com.fj,com.fr,com.gl,com.gt,com.hk,com.hr,com.hu,com.kg,com.ki,com.lc,com.mg,com.mm,com.ms,com.mt,com.mu,com.mx,com.my,com.na,com.nf,com.ng,com.ni,com.pa,com.ph,com.pl,com.pt,com.qa,com.ro,com.ru,com.sb,com.sc,com.sg,com.sv,com.tr,com.tw,com.ua,com.uy,com.ve,com.vn,cp.tz,cq.cn,de.com,de.org,ecn.br,ed.jp,edu.au,edu.cn,edu.hk,edu.mm,edu.my,edu.pl,edu.pt,edu.qa,edu.sg,edu.tr,edu.tw,eng.br,ernet.in,esp.br,etc.br,eti.br,eu.com,eu.int,eu.lv,firm.in,firm.ro,fm.br,fot.br,fst.br,g12.br,gb.com,gb.net,gd.cn,gen.in,go.jp,go.kr,go.th,gov.au,gov.az,gov.br,gov.cn,gov.il,gov.in,gov.mm,gov.my,gov.qa,gov.sg,gov.tr,gov.tw,gov.uk,gr.jp,gs.cn,gv.ac,gv.at,gx.cn,gz.cn,he.cn,hi.cn,hk.cn,hl.cn,hu.com,id.au,idv.tw,in.ua,ind.br,ind.in,inf.br,info.pl,info.ro,info.tr,info.ve,iwi.nz,jl.cn,jor.br,js.cn,jus.br,k12.il,k12.tr,kr.com,lel.br,lg.jp,ln.cn,ltd.uk,maori.nz,mb.ca,me.uk,med.br,mi.th,mil.br,mil.uk,mo.cn,mod.uk,muni.il,nb.ca,ne.jp,ne.kr,net.ag,net.ai,net.au,net.br,net.cn,net.do,net.gl,net.hk,net.il,net.in,net.kg,net.ki,net.lc,net.mg,net.mm,net.mu,net.ni,net.nz,net.pl,net.ru,net.sb,net.sc,net.sg,net.th,net.tr,net.tw,net.uk,net.ve,nf.ca,nhs.uk,nm.cn,nm.kr,no.com,nom.br,nom.ni,nom.ro,ns.ca,nt.ca,nt.ro,ntr.br,nx.cn,odo.br,off.ai,on.ca,or.ac,or.at,or.jp,or.kr,or.th,org.ag,org.ai,org.au,org.br,org.cn,org.do,org.es,org.gl,org.hk,org.in,org.kg,org.ki,org.lc,org.mg,org.mm,org.ms,org.nf,org.ni,org.nz,org.pl,org.ro,org.ru,org.sb,org.sc,org.sg,org.tr,org.tw,org.uk,org.ve,pe.ca,plc.uk,police.uk,ppg.br,presse.fr,pro.br,psc.br,psi.br,qc.ca,qc.com,qh.cn,rec.br,rec.ro,res.in,sa.com,sc.cn,sch.uk,se.com,se.net,sh.cn,sk.ca,slg.br,sn.cn,store.ro,tj.cn,tm.fr,tm.mc,tm.ro,tmp.br,tur.br,tv.br,tv.tr,tw.cn,uk.com,uk.net,us.com,uy.com,vet.br,waw.pl,web.ve,www.ro,xj.cn,xz.cn,yk.ca,yn.cn,zj.cn,zlg.br".split(",");
                    e = e.toLowerCase();
                    var n = e.split("."),
                        r = n.length;
                    if (2 > r || /^\d+$/.test(n[r - 1])) return e;
                    var o = n.slice(-2).join(".");
                    return r >= 3 && t.indexOf(o) >= 0 ? n.slice(-3).join(".") : o
                }

                function i(e, t) {
                    t = o(t);
                    for (var n = 0, r = t.length - 1; r >= 0; r--) n += 42403449800 * t.charCodeAt(r);
                    for (n = ("" + n).substring(0, 7), r = 0; r < e.length; r++)
                        if (n === e[r].substring(1, 8)) return 1
                }
                var a = function(e, t) {
                        var n = e.className.split(" "); - 1 === n.indexOf(t) && (e.className += " " + t)
                    },
                    l = function(e) {
                        return "none" !== window.getComputedStyle(e).display
                    },
                    s = e.conf,
                    u = r.common,
                    c = u.createElement,
                    f = s.swf.indexOf("flowplayer.org") && s.e && t.getAttribute("data-origin"),
                    d = f ? n(f) : u.hostname(),
                    p = (document, s.key);
                "file:" == location.protocol && (d = "localhost"), e.load.ed = 1, s.hostname = d, s.origin = f || location.href, f && a(t, "is-embedded"), "string" == typeof p && (p = p.split(/,\s*/));
                var m = function(e, n) {
                    var r = c("a", {
                        href: n,
                        className: "fp-brand"
                    });
                    r.innerHTML = e, u.find(".fp-controls", t)[0].appendChild(r)
                };
                if (p && "function" == typeof i && i(p, d)) {
                    if (s.logo) {
                        var v = u.find(".fp-player", t)[0],
                            g = c("a", {
                                className: "fp-logo"
                            });
                        f && (g.href = f), s.embed && s.embed.popup && (g.target = "_blank");
                        var h = c("img", {
                            src: s.logo
                        });
                        g.appendChild(h), (v || t).appendChild(g)
                    }
                    s.brand && f || s.brand && s.brand.showOnOrigin ? m(s.brand.text || s.brand, f || location.href) : u.addClass(t, "no-brand")
                } else {
                    m("flowplayer", "http://flowplayer.org");
                    var g = c("a", {
                        href: "http://flowplayer.org"
                    });
                    t.appendChild(g);
                    var y = c("div", {
                            className: "fp-context-menu"
                        }, '<ul><li class="copyright">&copy; 2015</li><li><a href="http://flowplayer.org">About Flowplayer</a></li><li><a href="http://flowplayer.org/license">GPL based license</a></li></ul>'),
                        b = window.location.href.indexOf("localhost"),
                        v = u.find(".fp-player", t)[0];
                    7 !== b && (v || t).appendChild(y), e.on("pause resume finish unload ready", function(e, n) {
                        u.removeClass(t, "no-brand");
                        var r = -1;
                        if (n.video.src)
                            for (var o = [
                                    ["org", "flowplayer", "drive"],
                                    ["org", "flowplayer", "my"],
                                    ["org", "flowplayer", "cdn"]
                                ], i = 0; i < o.length && (r = n.video.src.indexOf("://" + o[i].reverse().join(".")), -1 === r); i++);
                        if ((4 === r || 5 === r) && u.addClass(t, "no-brand"), /pause|resume/.test(e.type) && "flash" != n.engine.engineName && 4 != r && 5 != r) {
                            var a = {
                                display: "block",
                                position: "absolute",
                                left: "16px",
                                bottom: "46px",
                                zIndex: 99999,
                                width: "100px",
                                height: "20px",
                                backgroundImage: "url(" + [".png", "logo", "/", ".net", ".cloudfront", "d32wqyuo10o653", "//"].reverse().join("") + ")"
                            };
                            for (var s in a) a.hasOwnProperty(s) && (g.style[s] = a[s]);
                            n.load.ed = l(g) && (7 === b || y.parentNode == t || y.parentNode == v) && !u.hasClass(t, "no-brand"), n.load.ed || n.pause()
                        } else g.style.display = "none"
                    })
                }
            })
        }, {
            "./engine/embed": 2,
            "./engine/flash": 3,
            "./engine/html5": 4,
            "./ext/analytics": 5,
            "./ext/cuepoint": 6,
            "./ext/embed": 7,
            "./ext/fullscreen": 9,
            "./ext/keyboard": 10,
            "./ext/mobile": 11,
            "./ext/playlist": 12,
            "./ext/subtitle": 15,
            "./ext/support": 16,
            "./ext/ui": 17,
            "./flowplayer": 18,
            "es5-shim": 24
        }],
        20: [function(t, n, r) {
            ! function(t, r, o) {
                "undefined" != typeof n && n.exports ? n.exports = o() : "function" == typeof e && e.amd ? e(o) : r[t] = o()
            }("bean", this, function(e, t) {
                e = e || "bean", t = t || this;
                var n, r = window,
                    o = t[e],
                    i = /[^\.]*(?=\..*)\.|.*/,
                    a = /\..*/,
                    l = "addEventListener",
                    s = "removeEventListener",
                    u = document || {},
                    c = u.documentElement || {},
                    f = c[l],
                    d = f ? l : "attachEvent",
                    p = {},
                    m = Array.prototype.slice,
                    v = function(e, t) {
                        return e.split(t || " ")
                    },
                    g = function(e) {
                        return "string" == typeof e
                    },
                    h = function(e) {
                        return "function" == typeof e
                    },
                    y = "click dblclick mouseup mousedown contextmenu mousewheel mousemultiwheel DOMMouseScroll mouseover mouseout mousemove selectstart selectend keydown keypress keyup orientationchange focus blur change reset select submit load unload beforeunload resize move DOMContentLoaded readystatechange message error abort scroll ",
                    b = "show input invalid touchstart touchmove touchend touchcancel gesturestart gesturechange gestureend textinput readystatechange pageshow pagehide popstate hashchange offline online afterprint beforeprint dragstart dragenter dragover dragleave drag drop dragend loadstart progress suspend emptied stalled loadmetadata loadeddata canplay canplaythrough playing waiting seeking seeked ended durationchange timeupdate play pause ratechange volumechange cuechange checking noupdate downloading cached updateready obsolete ",
                    w = function(e, t, n) {
                        for (n = 0; n < t.length; n++) t[n] && (e[t[n]] = 1);
                        return e
                    }({}, v(y + (f ? b : ""))),
                    x = function() {
                        var e = "compareDocumentPosition" in c ? function(e, t) {
                                return t.compareDocumentPosition && 16 === (16 & t.compareDocumentPosition(e))
                            } : "contains" in c ? function(e, t) {
                                return t = 9 === t.nodeType || t === window ? c : t, t !== e && t.contains(e)
                            } : function(e, t) {
                                for (; e = e.parentNode;)
                                    if (e === t) return 1;
                                return 0
                            },
                            t = function(t) {
                                var n = t.relatedTarget;
                                return n ? n !== this && "xul" !== n.prefix && !/document/.test(this.toString()) && !e(n, this) : null == n
                            };
                        return {
                            mouseenter: {
                                base: "mouseover",
                                condition: t
                            },
                            mouseleave: {
                                base: "mouseout",
                                condition: t
                            },
                            mousewheel: {
                                base: /Firefox/.test(navigator.userAgent) ? "DOMMouseScroll" : "mousewheel"
                            }
                        }
                    }(),
                    E = function() {
                        var e = v("altKey attrChange attrName bubbles cancelable ctrlKey currentTarget detail eventPhase getModifierState isTrusted metaKey relatedNode relatedTarget shiftKey srcElement target timeStamp type view which propertyName"),
                            t = e.concat(v("button buttons clientX clientY dataTransfer fromElement offsetX offsetY pageX pageY screenX screenY toElement")),
                            n = t.concat(v("wheelDelta wheelDeltaX wheelDeltaY wheelDeltaZ axis")),
                            o = e.concat(v("char charCode key keyCode keyIdentifier keyLocation location")),
                            i = e.concat(v("data")),
                            a = e.concat(v("touches targetTouches changedTouches scale rotation")),
                            l = e.concat(v("data origin source")),
                            s = e.concat(v("state")),
                            f = /over|out/,
                            d = [{
                                reg: /key/i,
                                fix: function(e, t) {
                                    return t.keyCode = e.keyCode || e.which, o
                                }
                            }, {
                                reg: /click|mouse(?!(.*wheel|scroll))|menu|drag|drop/i,
                                fix: function(e, n, r) {
                                    return n.rightClick = 3 === e.which || 2 === e.button, n.pos = {
                                        x: 0,
                                        y: 0
                                    }, e.pageX || e.pageY ? (n.clientX = e.pageX, n.clientY = e.pageY) : (e.clientX || e.clientY) && (n.clientX = e.clientX + u.body.scrollLeft + c.scrollLeft, n.clientY = e.clientY + u.body.scrollTop + c.scrollTop), f.test(r) && (n.relatedTarget = e.relatedTarget || e[("mouseover" == r ? "from" : "to") + "Element"]), t
                                }
                            }, {
                                reg: /mouse.*(wheel|scroll)/i,
                                fix: function() {
                                    return n
                                }
                            }, {
                                reg: /^text/i,
                                fix: function() {
                                    return i
                                }
                            }, {
                                reg: /^touch|^gesture/i,
                                fix: function() {
                                    return a
                                }
                            }, {
                                reg: /^message$/i,
                                fix: function() {
                                    return l
                                }
                            }, {
                                reg: /^popstate$/i,
                                fix: function() {
                                    return s
                                }
                            }, {
                                reg: /.*/,
                                fix: function() {
                                    return e
                                }
                            }],
                            p = {},
                            m = function(e, t, n) {
                                if (arguments.length && (e = e || ((t.ownerDocument || t.document || t).parentWindow || r).event, this.originalEvent = e, this.isNative = n, this.isBean = !0, e)) {
                                    var o, i, a, l, s, u = e.type,
                                        c = e.target || e.srcElement;
                                    if (this.target = c && 3 === c.nodeType ? c.parentNode : c, n) {
                                        if (s = p[u], !s)
                                            for (o = 0, i = d.length; i > o; o++)
                                                if (d[o].reg.test(u)) {
                                                    p[u] = s = d[o].fix;
                                                    break
                                                } for (l = s(e, this, u), o = l.length; o--;) !((a = l[o]) in this) && a in e && (this[a] = e[a])
                                    }
                                }
                            };
                        return m.prototype.preventDefault = function() {
                            this.originalEvent.preventDefault ? this.originalEvent.preventDefault() : this.originalEvent.returnValue = !1
                        }, m.prototype.stopPropagation = function() {
                            this.originalEvent.stopPropagation ? this.originalEvent.stopPropagation() : this.originalEvent.cancelBubble = !0
                        }, m.prototype.stop = function() {
                            this.preventDefault(), this.stopPropagation(), this.stopped = !0
                        }, m.prototype.stopImmediatePropagation = function() {
                            this.originalEvent.stopImmediatePropagation && this.originalEvent.stopImmediatePropagation(), this.isImmediatePropagationStopped = function() {
                                return !0
                            }
                        }, m.prototype.isImmediatePropagationStopped = function() {
                            return this.originalEvent.isImmediatePropagationStopped && this.originalEvent.isImmediatePropagationStopped()
                        }, m.prototype.clone = function(e) {
                            var t = new m(this, this.element, this.isNative);
                            return t.currentTarget = e, t
                        }, m
                    }(),
                    k = function(e, t) {
                        return f || t || e !== u && e !== r ? e : c
                    },
                    T = function() {
                        var e = function(e, t, n, r) {
                                var o = function(n, o) {
                                        return t.apply(e, r ? m.call(o, n ? 0 : 1).concat(r) : o)
                                    },
                                    i = function(n, r) {
                                        return t.__beanDel ? t.__beanDel.ft(n.target, e) : r
                                    },
                                    a = n ? function(e) {
                                        var t = i(e, this);
                                        return n.apply(t, arguments) ? (e && (e.currentTarget = t), o(e, arguments)) : void 0
                                    } : function(e) {
                                        return t.__beanDel && (e = e.clone(i(e))), o(e, arguments)
                                    };
                                return a.__beanDel = t.__beanDel, a
                            },
                            t = function(t, n, r, o, i, a, l) {
                                var s, u = x[n];
                                "unload" == n && (r = O(P, t, n, r, o)), u && (u.condition && (r = e(t, r, u.condition, a)), n = u.base || n), this.isNative = s = w[n] && !!t[d], this.customType = !f && !s && n, this.element = t, this.type = n, this.original = o, this.namespaces = i, this.eventType = f || s ? n : "propertychange", this.target = k(t, s), this[d] = !!this.target[d], this.root = l, this.handler = e(t, r, null, a)
                            };
                        return t.prototype.inNamespaces = function(e) {
                            var t, n, r = 0;
                            if (!e) return !0;
                            if (!this.namespaces) return !1;
                            for (t = e.length; t--;)
                                for (n = this.namespaces.length; n--;) e[t] == this.namespaces[n] && r++;
                            return e.length === r
                        }, t.prototype.matches = function(e, t, n) {
                            return !(this.element !== e || t && this.original !== t || n && this.handler !== n)
                        }, t
                    }(),
                    S = function() {
                        var e = {},
                            t = function(n, r, o, i, a, l) {
                                var s = a ? "r" : "$";
                                if (r && "*" != r) {
                                    var u, c = 0,
                                        f = e[s + r],
                                        d = "*" == n;
                                    if (!f) return;
                                    for (u = f.length; u > c; c++)
                                        if ((d || f[c].matches(n, o, i)) && !l(f[c], f, c, r)) return
                                } else
                                    for (var p in e) p.charAt(0) == s && t(n, p.substr(1), o, i, a, l)
                            },
                            n = function(t, n, r, o) {
                                var i, a = e[(o ? "r" : "$") + n];
                                if (a)
                                    for (i = a.length; i--;)
                                        if (!a[i].root && a[i].matches(t, r, null)) return !0;
                                return !1
                            },
                            r = function(e, n, r, o) {
                                var i = [];
                                return t(e, n, r, null, o, function(e) {
                                    return i.push(e)
                                }), i
                            },
                            o = function(t) {
                                var n = !t.root && !this.has(t.element, t.type, null, !1),
                                    r = (t.root ? "r" : "$") + t.type;
                                return (e[r] || (e[r] = [])).push(t), n
                            },
                            i = function(n) {
                                t(n.element, n.type, null, n.handler, n.root, function(t, n, r) {
                                    return n.splice(r, 1), t.removed = !0, 0 === n.length && delete e[(t.root ? "r" : "$") + t.type], !1
                                })
                            },
                            a = function() {
                                var t, n = [];
                                for (t in e) "$" == t.charAt(0) && (n = n.concat(e[t]));
                                return n
                            };
                        return {
                            has: n,
                            get: r,
                            put: o,
                            del: i,
                            entries: a
                        }
                    }(),
                    N = function(e) {
                        n = arguments.length ? e : u.querySelectorAll ? function(e, t) {
                            return t.querySelectorAll(e)
                        } : function() {
                            throw new Error("Bean: No selector engine installed")
                        }
                    },
                    C = function(e, t) {
                        if (f || !t || !e || e.propertyName == "_on" + t) {
                            var n = S.get(this, t || e.type, null, !1),
                                r = n.length,
                                o = 0;
                            for (e = new E(e, this, !0), t && (e.type = t); r > o && !e.isImmediatePropagationStopped(); o++) n[o].removed || n[o].handler.call(this, e)
                        }
                    },
                    j = f ? function(e, t, n) {
                        e[n ? l : s](t, C, !1)
                    } : function(e, t, n, r) {
                        var o;
                        n ? (S.put(o = new T(e, r || t, function(t) {
                            C.call(e, t, r)
                        }, C, null, null, !0)), r && null == e["_on" + r] && (e["_on" + r] = 0), o.target.attachEvent("on" + o.eventType, o.handler)) : (o = S.get(e, r || t, C, !0)[0], o && (o.target.detachEvent("on" + o.eventType, o.handler), S.del(o)))
                    },
                    O = function(e, t, n, r, o) {
                        return function() {
                            r.apply(this, arguments), e(t, n, o)
                        }
                    },
                    P = function(e, t, n, r) {
                        var o, i, l = t && t.replace(a, ""),
                            s = S.get(e, l, null, !1),
                            u = {};
                        for (o = 0, i = s.length; i > o; o++) n && s[o].original !== n || !s[o].inNamespaces(r) || (S.del(s[o]), !u[s[o].eventType] && s[o][d] && (u[s[o].eventType] = {
                            t: s[o].eventType,
                            c: s[o].type
                        }));
                        for (o in u) S.has(e, u[o].t, null, !1) || j(e, u[o].t, !1, u[o].c)
                    },
                    A = function(e, t) {
                        var r = function(t, r) {
                                for (var o, i = g(e) ? n(e, r) : e; t && t !== r; t = t.parentNode)
                                    for (o = i.length; o--;)
                                        if (i[o] === t) return t
                            },
                            o = function(e) {
                                var n = r(e.target, this);
                                n && t.apply(n, arguments)
                            };
                        return o.__beanDel = {
                            ft: r,
                            selector: e
                        }, o
                    },
                    _ = f ? function(e, t, n) {
                        var o = u.createEvent(e ? "HTMLEvents" : "UIEvents");
                        o[e ? "initEvent" : "initUIEvent"](t, !0, !0, r, 1), n.dispatchEvent(o)
                    } : function(e, t, n) {
                        n = k(n, e), e ? n.fireEvent("on" + t, u.createEventObject()) : n["_on" + t]++
                    },
                    D = function(e, t, n) {
                        var r, o, l, s, u = g(t);
                        if (u && t.indexOf(" ") > 0) {
                            for (t = v(t), s = t.length; s--;) D(e, t[s], n);
                            return e
                        }
                        if (o = u && t.replace(a, ""), o && x[o] && (o = x[o].base), !t || u)(l = u && t.replace(i, "")) && (l = v(l, ".")), P(e, o, n, l);
                        else if (h(t)) P(e, null, t);
                        else
                            for (r in t) t.hasOwnProperty(r) && D(e, r, t[r]);
                        return e
                    },
                    M = function(e, t, r, o) {
                        var l, s, u, c, f, g, y; {
                            if (void 0 !== r || "object" != typeof t) {
                                for (h(r) ? (f = m.call(arguments, 3), o = l = r) : (l = o, f = m.call(arguments, 4), o = A(r, l, n)), u = v(t), this === p && (o = O(D, e, t, o, l)), c = u.length; c--;) y = S.put(g = new T(e, u[c].replace(a, ""), o, l, v(u[c].replace(i, ""), "."), f, !1)), g[d] && y && j(e, g.eventType, !0, g.customType);
                                return e
                            }
                            for (s in t) t.hasOwnProperty(s) && M.call(this, e, s, t[s])
                        }
                    },
                    L = function(e, t, n, r) {
                        return M.apply(null, g(n) ? [e, n, t, r].concat(arguments.length > 3 ? m.call(arguments, 5) : []) : m.call(arguments))
                    },
                    I = function() {
                        return M.apply(p, arguments)
                    },
                    F = function(e, t, n) {
                        var r, o, l, s, u, c = v(t);
                        for (r = c.length; r--;)
                            if (t = c[r].replace(a, ""), (s = c[r].replace(i, "")) && (s = v(s, ".")), s || n || !e[d])
                                for (u = S.get(e, t, null, !1), n = [!1].concat(n), o = 0, l = u.length; l > o; o++) u[o].inNamespaces(s) && u[o].handler.apply(e, n);
                            else _(w[t], t, e);
                        return e
                    },
                    z = function(e, t, n) {
                        for (var r, o, i = S.get(t, n, null, !1), a = i.length, l = 0; a > l; l++) i[l].original && (r = [e, i[l].type], (o = i[l].handler.__beanDel) && r.push(o.selector), r.push(i[l].original), M.apply(null, r));
                        return e
                    },
                    R = {
                        on: M,
                        add: L,
                        one: I,
                        off: D,
                        remove: D,
                        clone: z,
                        fire: F,
                        Event: E,
                        setSelectorEngine: N,
                        noConflict: function() {
                            return t[e] = o, this
                        }
                    };
                if (r.attachEvent) {
                    var q = function() {
                        var e, t = S.entries();
                        for (e in t) t[e].type && "unload" !== t[e].type && D(t[e].element, t[e].type);
                        r.detachEvent("onunload", q), r.CollectGarbage && r.CollectGarbage()
                    };
                    r.attachEvent("onunload", q)
                }
                return N(), R
            })
        }, {}],
        21: [function(e, t, n) {
            function r(e) {
                function t(e) {
                    var t = c();
                    a(t, e) > -1 || (t.push(e), f(t))
                }

                function n(e) {
                    var t = c(),
                        n = a(t, e); - 1 !== n && (t.splice(n, 1), f(t))
                }

                function r(e) {
                    return a(c(), e) > -1
                }

                function l(e) {
                    return r(e) ? (n(e), !1) : (t(e), !0)
                }

                function s() {
                    return e.className
                }

                function u(e) {
                    var t = c();
                    return t[e] || null
                }

                function c() {
                    var t = e.className;
                    return o(t.split(" "), i)
                }

                function f(t) {
                    var n = t.length;
                    e.className = t.join(" "), p.length = n;
                    for (var r = 0; r < t.length; r++) p[r] = t[r];
                    delete t[n]
                }
                var d = e.classList;
                if (d) return d;
                var p = {
                    add: t,
                    remove: n,
                    contains: r,
                    toggle: l,
                    toString: s,
                    length: 0,
                    item: u
                };
                return p
            }

            function o(e, t) {
                for (var n = [], r = 0; r < e.length; r++) t(e[r]) && n.push(e[r]);
                return n
            }

            function i(e) {
                return !!e
            }
            var a = e("indexof");
            t.exports = r
        }, {
            indexof: 22
        }],
        22: [function(e, t, n) {
            var r = [].indexOf;
            t.exports = function(e, t) {
                if (r) return e.indexOf(t);
                for (var n = 0; n < e.length; ++n)
                    if (e[n] === t) return n;
                return -1
            }
        }, {}],
        23: [function(e, t, n) {
            function r(e, t, n, r) {
                return n = window.getComputedStyle, r = n ? n(e) : e.currentStyle, r ? r[t.replace(/-(\w)/gi, function(e, t) {
                    return t.toUpperCase()
                })] : void 0
            }
            t.exports = r
        }, {}],
        24: [function(t, n, r) {
            ! function(t, o) {
                "use strict";
                "function" == typeof e && e.amd ? e(o) : "object" == typeof r ? n.exports = o() : t.returnExports = o()
            }(this, function() {
                var e, t = Array.prototype,
                    n = Object.prototype,
                    r = Function.prototype,
                    o = String.prototype,
                    i = Number.prototype,
                    a = t.slice,
                    l = t.splice,
                    s = t.push,
                    u = t.unshift,
                    c = t.concat,
                    f = r.call,
                    d = n.toString,
                    p = Array.isArray || function(e) {
                        return "[object Array]" === d.call(e)
                    },
                    m = "function" == typeof Symbol && "symbol" == typeof Symbol.toStringTag,
                    v = Function.prototype.toString,
                    g = function(e) {
                        try {
                            return v.call(e), !0
                        } catch (t) {
                            return !1
                        }
                    },
                    h = "[object Function]",
                    y = "[object GeneratorFunction]";
                e = function(e) {
                    if ("function" != typeof e) return !1;
                    if (m) return g(e);
                    var t = d.call(e);
                    return t === h || t === y
                };
                var b, w = RegExp.prototype.exec,
                    x = function(e) {
                        try {
                            return w.call(e), !0
                        } catch (t) {
                            return !1
                        }
                    },
                    E = "[object RegExp]";
                b = function(e) {
                    return "object" != typeof e ? !1 : m ? x(e) : d.call(e) === E
                };
                var k, T = String.prototype.valueOf,
                    S = function(e) {
                        try {
                            return T.call(e), !0
                        } catch (t) {
                            return !1
                        }
                    },
                    N = "[object String]";
                k = function(e) {
                    return "string" == typeof e ? !0 : "object" != typeof e ? !1 : m ? S(e) : d.call(e) === N
                };
                var C = function(t) {
                        var n = d.call(t),
                            r = "[object Arguments]" === n;
                        return r || (r = !p(t) && null !== t && "object" == typeof t && "number" == typeof t.length && t.length >= 0 && e(t.callee)), r
                    },
                    j = function(e) {
                        var t, n = Object.defineProperty && function() {
                            try {
                                var e = {};
                                Object.defineProperty(e, "x", {
                                    enumerable: !1,
                                    value: e
                                });
                                for (var t in e) return !1;
                                return e.x === e
                            } catch (n) {
                                return !1
                            }
                        }();
                        return t = n ? function(e, t, n, r) {
                                !r && t in e || Object.defineProperty(e, t, {
                                    configurable: !0,
                                    enumerable: !1,
                                    writable: !0,
                                    value: n
                                })
                            } : function(e, t, n, r) {
                                !r && t in e || (e[t] = n)
                            },
                            function(n, r, o) {
                                for (var i in r) e.call(r, i) && t(n, i, r[i], o)
                            }
                    }(n.hasOwnProperty),
                    O = function(e) {
                        var t = typeof e;
                        return null === e || "object" !== t && "function" !== t
                    },
                    P = {
                        ToInteger: function(e) {
                            var t = +e;
                            return t !== t ? t = 0 : 0 !== t && t !== 1 / 0 && t !== -(1 / 0) && (t = (t > 0 || -1) * Math.floor(Math.abs(t))), t
                        },
                        ToPrimitive: function(t) {
                            var n, r, o;
                            if (O(t)) return t;
                            if (r = t.valueOf, e(r) && (n = r.call(t), O(n))) return n;
                            if (o = t.toString, e(o) && (n = o.call(t), O(n))) return n;
                            throw new TypeError
                        },
                        ToObject: function(e) {
                            if (null == e) throw new TypeError("can't convert " + e + " to object");
                            return Object(e)
                        },
                        ToUint32: function(e) {
                            return e >>> 0
                        }
                    },
                    A = function() {};
                j(r, {
                    bind: function(t) {
                        var n = this;
                        if (!e(n)) throw new TypeError("Function.prototype.bind called on incompatible " + n);
                        for (var r, o = a.call(arguments, 1), i = function() {
                                if (this instanceof r) {
                                    var e = n.apply(this, c.call(o, a.call(arguments)));
                                    return Object(e) === e ? e : this
                                }
                                return n.apply(t, c.call(o, a.call(arguments)))
                            }, l = Math.max(0, n.length - o.length), s = [], u = 0; l > u; u++) s.push("$" + u);
                        return r = Function("binder", "return function (" + s.join(",") + "){ return binder.apply(this, arguments); }")(i), n.prototype && (A.prototype = n.prototype, r.prototype = new A, A.prototype = null), r
                    }
                });
                var _ = f.bind(n.hasOwnProperty),
                    D = function() {
                        var e = [1, 2],
                            t = e.splice();
                        return 2 === e.length && p(t) && 0 === t.length
                    }();
                j(t, {
                    splice: function(e, t) {
                        return 0 === arguments.length ? [] : l.apply(this, arguments)
                    }
                }, !D);
                var M = function() {
                    var e = {};
                    return t.splice.call(e, 0, 0, 1), 1 === e.length
                }();
                j(t, {
                    splice: function(e, t) {
                        if (0 === arguments.length) return [];
                        var n = arguments;
                        return this.length = Math.max(P.ToInteger(this.length), 0), arguments.length > 0 && "number" != typeof t && (n = a.call(arguments), n.length < 2 ? n.push(this.length - e) : n[1] = P.ToInteger(t)), l.apply(this, n)
                    }
                }, !M);
                var L = 1 !== [].unshift(0);
                j(t, {
                    unshift: function() {
                        return u.apply(this, arguments), this.length
                    }
                }, L), j(Array, {
                    isArray: p
                });
                var I = Object("a"),
                    F = "a" !== I[0] || !(0 in I),
                    z = function(e) {
                        var t = !0,
                            n = !0;
                        return e && (e.call("foo", function(e, n, r) {
                            "object" != typeof r && (t = !1)
                        }), e.call([1], function() {
                            "use strict";
                            n = "string" == typeof this
                        }, "x")), !!e && t && n
                    };
                j(t, {
                    forEach: function(t) {
                        var n, r = P.ToObject(this),
                            o = F && k(this) ? this.split("") : r,
                            i = -1,
                            a = o.length >>> 0;
                        if (arguments.length > 1 && (n = arguments[1]), !e(t)) throw new TypeError("Array.prototype.forEach callback must be a function");
                        for (; ++i < a;) i in o && ("undefined" != typeof n ? t.call(n, o[i], i, r) : t(o[i], i, r))
                    }
                }, !z(t.forEach)), j(t, {
                    map: function(t) {
                        var n, r = P.ToObject(this),
                            o = F && k(this) ? this.split("") : r,
                            i = o.length >>> 0,
                            a = Array(i);
                        if (arguments.length > 1 && (n = arguments[1]), !e(t)) throw new TypeError("Array.prototype.map callback must be a function");
                        for (var l = 0; i > l; l++) l in o && ("undefined" != typeof n ? a[l] = t.call(n, o[l], l, r) : a[l] = t(o[l], l, r));
                        return a
                    }
                }, !z(t.map)), j(t, {
                    filter: function(t) {
                        var n, r, o = P.ToObject(this),
                            i = F && k(this) ? this.split("") : o,
                            a = i.length >>> 0,
                            l = [];
                        if (arguments.length > 1 && (r = arguments[1]), !e(t)) throw new TypeError("Array.prototype.filter callback must be a function");
                        for (var s = 0; a > s; s++) s in i && (n = i[s], ("undefined" == typeof r ? t(n, s, o) : t.call(r, n, s, o)) && l.push(n));
                        return l
                    }
                }, !z(t.filter)), j(t, {
                    every: function(t) {
                        var n, r = P.ToObject(this),
                            o = F && k(this) ? this.split("") : r,
                            i = o.length >>> 0;
                        if (arguments.length > 1 && (n = arguments[1]), !e(t)) throw new TypeError("Array.prototype.every callback must be a function");
                        for (var a = 0; i > a; a++)
                            if (a in o && !("undefined" == typeof n ? t(o[a], a, r) : t.call(n, o[a], a, r))) return !1;
                        return !0
                    }
                }, !z(t.every)), j(t, {
                    some: function(t) {
                        var n, r = P.ToObject(this),
                            o = F && k(this) ? this.split("") : r,
                            i = o.length >>> 0;
                        if (arguments.length > 1 && (n = arguments[1]), !e(t)) throw new TypeError("Array.prototype.some callback must be a function");
                        for (var a = 0; i > a; a++)
                            if (a in o && ("undefined" == typeof n ? t(o[a], a, r) : t.call(n, o[a], a, r))) return !0;
                        return !1
                    }
                }, !z(t.some));
                var R = !1;
                t.reduce && (R = "object" == typeof t.reduce.call("es5", function(e, t, n, r) {
                    return r
                })), j(t, {
                    reduce: function(t) {
                        var n = P.ToObject(this),
                            r = F && k(this) ? this.split("") : n,
                            o = r.length >>> 0;
                        if (!e(t)) throw new TypeError("Array.prototype.reduce callback must be a function");
                        if (0 === o && 1 === arguments.length) throw new TypeError("reduce of empty array with no initial value");
                        var i, a = 0;
                        if (arguments.length >= 2) i = arguments[1];
                        else
                            for (;;) {
                                if (a in r) {
                                    i = r[a++];
                                    break
                                }
                                if (++a >= o) throw new TypeError("reduce of empty array with no initial value")
                            }
                        for (; o > a; a++) a in r && (i = t(i, r[a], a, n));
                        return i
                    }
                }, !R);
                var q = !1;
                t.reduceRight && (q = "object" == typeof t.reduceRight.call("es5", function(e, t, n, r) {
                    return r
                })), j(t, {
                    reduceRight: function(t) {
                        var n = P.ToObject(this),
                            r = F && k(this) ? this.split("") : n,
                            o = r.length >>> 0;
                        if (!e(t)) throw new TypeError("Array.prototype.reduceRight callback must be a function");
                        if (0 === o && 1 === arguments.length) throw new TypeError("reduceRight of empty array with no initial value");
                        var i, a = o - 1;
                        if (arguments.length >= 2) i = arguments[1];
                        else
                            for (;;) {
                                if (a in r) {
                                    i = r[a--];
                                    break
                                }
                                if (--a < 0) throw new TypeError("reduceRight of empty array with no initial value")
                            }
                        if (0 > a) return i;
                        do a in r && (i = t(i, r[a], a, n)); while (a--);
                        return i
                    }
                }, !q);
                var V = Array.prototype.indexOf && -1 !== [0, 1].indexOf(1, 2);
                j(t, {
                    indexOf: function(e) {
                        var t = F && k(this) ? this.split("") : P.ToObject(this),
                            n = t.length >>> 0;
                        if (0 === n) return -1;
                        var r = 0;
                        for (arguments.length > 1 && (r = P.ToInteger(arguments[1])), r = r >= 0 ? r : Math.max(0, n + r); n > r; r++)
                            if (r in t && t[r] === e) return r;
                        return -1
                    }
                }, V);
                var H = Array.prototype.lastIndexOf && -1 !== [0, 1].lastIndexOf(0, -3);
                j(t, {
                    lastIndexOf: function(e) {
                        var t = F && k(this) ? this.split("") : P.ToObject(this),
                            n = t.length >>> 0;
                        if (0 === n) return -1;
                        var r = n - 1;
                        for (arguments.length > 1 && (r = Math.min(r, P.ToInteger(arguments[1]))), r = r >= 0 ? r : n - Math.abs(r); r >= 0; r--)
                            if (r in t && e === t[r]) return r;
                        return -1
                    }
                }, H);
                var U = !{
                        toString: null
                    }.propertyIsEnumerable("toString"),
                    $ = function() {}.propertyIsEnumerable("prototype"),
                    X = !_("x", "0"),
                    Y = ["toString", "toLocaleString", "valueOf", "hasOwnProperty", "isPrototypeOf", "propertyIsEnumerable", "constructor"],
                    B = Y.length;
                j(Object, {
                    keys: function(t) {
                        var n = e(t),
                            r = C(t),
                            o = null !== t && "object" == typeof t,
                            i = o && k(t);
                        if (!o && !n && !r) throw new TypeError("Object.keys called on a non-object");
                        var a = [],
                            l = $ && n;
                        if (i && X || r)
                            for (var s = 0; s < t.length; ++s) a.push(String(s));
                        if (!r)
                            for (var u in t) l && "prototype" === u || !_(t, u) || a.push(String(u));
                        if (U)
                            for (var c = t.constructor, f = c && c.prototype === t, d = 0; B > d; d++) {
                                var p = Y[d];
                                f && "constructor" === p || !_(t, p) || a.push(p)
                            }
                        return a
                    }
                });
                var W = Object.keys && function() {
                        return 2 === Object.keys(arguments).length
                    }(1, 2),
                    K = Object.keys;
                j(Object, {
                    keys: function(e) {
                        return K(C(e) ? t.slice.call(e) : e)
                    }
                }, !W);
                var G = -621987552e5,
                    Z = "-000001",
                    J = Date.prototype.toISOString && -1 === new Date(G).toISOString().indexOf(Z);
                j(Date.prototype, {
                    toISOString: function() {
                        var e, t, n, r, o;
                        if (!isFinite(this)) throw new RangeError("Date.prototype.toISOString called on non-finite value.");
                        for (r = this.getUTCFullYear(), o = this.getUTCMonth(), r += Math.floor(o / 12), o = (o % 12 + 12) % 12, e = [o + 1, this.getUTCDate(), this.getUTCHours(), this.getUTCMinutes(), this.getUTCSeconds()], r = (0 > r ? "-" : r > 9999 ? "+" : "") + ("00000" + Math.abs(r)).slice(r >= 0 && 9999 >= r ? -4 : -6), t = e.length; t--;) n = e[t], 10 > n && (e[t] = "0" + n);
                        return r + "-" + e.slice(0, 2).join("-") + "T" + e.slice(2).join(":") + "." + ("000" + this.getUTCMilliseconds()).slice(-3) + "Z"
                    }
                }, J);
                var Q = function() {
                    try {
                        return Date.prototype.toJSON && null === new Date(NaN).toJSON() && -1 !== new Date(G).toJSON().indexOf(Z) && Date.prototype.toJSON.call({
                            toISOString: function() {
                                return !0
                            }
                        })
                    } catch (e) {
                        return !1
                    }
                }();
                Q || (Date.prototype.toJSON = function(t) {
                    var n = Object(this),
                        r = P.ToPrimitive(n);
                    if ("number" == typeof r && !isFinite(r)) return null;
                    var o = n.toISOString;
                    if (!e(o)) throw new TypeError("toISOString property is not callable");
                    return o.call(n)
                });
                var ee = 1e15 === Date.parse("+033658-09-27T01:46:40.000Z"),
                    te = !isNaN(Date.parse("2012-04-04T24:00:00.500Z")) || !isNaN(Date.parse("2012-11-31T23:59:59.000Z")) || !isNaN(Date.parse("2012-12-31T23:59:60.000Z")),
                    ne = isNaN(Date.parse("2000-01-01T00:00:00.000Z"));
                (!Date.parse || ne || te || !ee) && (Date = function(e) {
                    var t = function(n, r, o, i, a, l, s) {
                            var u, c = arguments.length;
                            return u = this instanceof e ? 1 === c && String(n) === n ? new e(t.parse(n)) : c >= 7 ? new e(n, r, o, i, a, l, s) : c >= 6 ? new e(n, r, o, i, a, l) : c >= 5 ? new e(n, r, o, i, a) : c >= 4 ? new e(n, r, o, i) : c >= 3 ? new e(n, r, o) : c >= 2 ? new e(n, r) : c >= 1 ? new e(n) : new e : e.apply(this, arguments), j(u, {
                                constructor: t
                            }, !0), u
                        },
                        n = new RegExp("^(\\d{4}|[+-]\\d{6})(?:-(\\d{2})(?:-(\\d{2})(?:T(\\d{2}):(\\d{2})(?::(\\d{2})(?:(\\.\\d{1,}))?)?(Z|(?:([-+])(\\d{2}):(\\d{2})))?)?)?)?$"),
                        r = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365],
                        o = function(e, t) {
                            var n = t > 1 ? 1 : 0;
                            return r[t] + Math.floor((e - 1969 + n) / 4) - Math.floor((e - 1901 + n) / 100) + Math.floor((e - 1601 + n) / 400) + 365 * (e - 1970)
                        },
                        i = function(t) {
                            return Number(new e(1970, 0, 1, 0, 0, 0, t))
                        };
                    for (var a in e) _(e, a) && (t[a] = e[a]);
                    return j(t, {
                        now: e.now,
                        UTC: e.UTC
                    }, !0), t.prototype = e.prototype, j(t.prototype, {
                        constructor: t
                    }, !0), t.parse = function(t) {
                        var r = n.exec(t);
                        if (r) {
                            var a, l = Number(r[1]),
                                s = Number(r[2] || 1) - 1,
                                u = Number(r[3] || 1) - 1,
                                c = Number(r[4] || 0),
                                f = Number(r[5] || 0),
                                d = Number(r[6] || 0),
                                p = Math.floor(1e3 * Number(r[7] || 0)),
                                m = Boolean(r[4] && !r[8]),
                                v = "-" === r[9] ? 1 : -1,
                                g = Number(r[10] || 0),
                                h = Number(r[11] || 0);
                            return (f > 0 || d > 0 || p > 0 ? 24 : 25) > c && 60 > f && 60 > d && 1e3 > p && s > -1 && 12 > s && 24 > g && 60 > h && u > -1 && u < o(l, s + 1) - o(l, s) && (a = 60 * (24 * (o(l, s) + u) + c + g * v), a = 1e3 * (60 * (a + f + h * v) + d) + p, m && (a = i(a)), a >= -864e13 && 864e13 >= a) ? a : NaN
                        }
                        return e.parse.apply(this, arguments)
                    }, t
                }(Date)), Date.now || (Date.now = function() {
                    return (new Date).getTime()
                });
                var re = i.toFixed && ("0.000" !== 8e-5.toFixed(3) || "1" !== .9.toFixed(0) || "1.25" !== 1.255.toFixed(2) || "1000000000000000128" !== 0xde0b6b3a7640080.toFixed(0)),
                    oe = {
                        base: 1e7,
                        size: 6,
                        data: [0, 0, 0, 0, 0, 0],
                        multiply: function(e, t) {
                            for (var n = -1, r = t; ++n < oe.size;) r += e * oe.data[n], oe.data[n] = r % oe.base, r = Math.floor(r / oe.base)
                        },
                        divide: function(e) {
                            for (var t = oe.size, n = 0; --t >= 0;) n += oe.data[t], oe.data[t] = Math.floor(n / e), n = n % e * oe.base
                        },
                        numToString: function() {
                            for (var e = oe.size, t = ""; --e >= 0;)
                                if ("" !== t || 0 === e || 0 !== oe.data[e]) {
                                    var n = String(oe.data[e]);
                                    "" === t ? t = n : t += "0000000".slice(0, 7 - n.length) + n
                                } return t
                        },
                        pow: function ge(e, t, n) {
                            return 0 === t ? n : t % 2 === 1 ? ge(e, t - 1, n * e) : ge(e * e, t / 2, n)
                        },
                        log: function(e) {
                            for (var t = 0, n = e; n >= 4096;) t += 12, n /= 4096;
                            for (; n >= 2;) t += 1, n /= 2;
                            return t
                        }
                    };
                j(i, {
                    toFixed: function(e) {
                        var t, n, r, o, i, a, l, s;
                        if (t = Number(e), t = t !== t ? 0 : Math.floor(t), 0 > t || t > 20) throw new RangeError("Number.toFixed called with invalid number of decimals");
                        if (n = Number(this), n !== n) return "NaN";
                        if (-1e21 >= n || n >= 1e21) return String(n);
                        if (r = "", 0 > n && (r = "-", n = -n), o = "0", n > 1e-21)
                            if (i = oe.log(n * oe.pow(2, 69, 1)) - 69, a = 0 > i ? n * oe.pow(2, -i, 1) : n / oe.pow(2, i, 1), a *= 4503599627370496, i = 52 - i, i > 0) {
                                for (oe.multiply(0, a), l = t; l >= 7;) oe.multiply(1e7, 0), l -= 7;
                                for (oe.multiply(oe.pow(10, l, 1), 0), l = i - 1; l >= 23;) oe.divide(1 << 23), l -= 23;
                                oe.divide(1 << l), oe.multiply(1, 1), oe.divide(2), o = oe.numToString()
                            } else oe.multiply(0, a), oe.multiply(1 << -i, 0), o = oe.numToString() + "0.00000000000000000000".slice(2, 2 + t);
                        return t > 0 ? (s = o.length, o = t >= s ? r + "0.0000000000000000000".slice(0, t - s + 2) + o : r + o.slice(0, s - t) + "." + o.slice(s - t)) : o = r + o, o
                    }
                }, re);
                var ie = o.split;
                2 !== "ab".split(/(?:ab)*/).length || 4 !== ".".split(/(.?)(.?)/).length || "t" === "tesst".split(/(s)*/)[1] || 4 !== "test".split(/(?:)/, -1).length || "".split(/.?/).length || ".".split(/()()/).length > 1 ? ! function() {
                    var e = "undefined" == typeof /()??/.exec("")[1];
                    o.split = function(t, n) {
                        var r = this;
                        if ("undefined" == typeof t && 0 === n) return [];
                        if (!b(t)) return ie.call(this, t, n);
                        var o, i, a, l, u = [],
                            c = (t.ignoreCase ? "i" : "") + (t.multiline ? "m" : "") + (t.extended ? "x" : "") + (t.sticky ? "y" : ""),
                            f = 0,
                            d = new RegExp(t.source, c + "g");
                        r += "", e || (o = new RegExp("^" + d.source + "$(?!\\s)", c));
                        var p = "undefined" == typeof n ? -1 >>> 0 : P.ToUint32(n);
                        for (i = d.exec(r); i && (a = i.index + i[0].length, !(a > f && (u.push(r.slice(f, i.index)), !e && i.length > 1 && i[0].replace(o, function() {
                                for (var e = 1; e < arguments.length - 2; e++) "undefined" == typeof arguments[e] && (i[e] = void 0)
                            }), i.length > 1 && i.index < r.length && s.apply(u, i.slice(1)), l = i[0].length, f = a, u.length >= p)));) d.lastIndex === i.index && d.lastIndex++, i = d.exec(r);
                        return f === r.length ? (l || !d.test("")) && u.push("") : u.push(r.slice(f)), u.length > p ? u.slice(0, p) : u
                    }
                }() : "0".split(void 0, 0).length && (o.split = function(e, t) {
                    return "undefined" == typeof e && 0 === t ? [] : ie.call(this, e, t)
                });
                var ae = o.replace,
                    le = function() {
                        var e = [];
                        return "x".replace(/x(.)?/g, function(t, n) {
                            e.push(n)
                        }), 1 === e.length && "undefined" == typeof e[0]
                    }();
                le || (o.replace = function(t, n) {
                    var r = e(n),
                        o = b(t) && /\)[*?]/.test(t.source);
                    if (r && o) {
                        var i = function(e) {
                            var r = arguments.length,
                                o = t.lastIndex;
                            t.lastIndex = 0;
                            var i = t.exec(e) || [];
                            return t.lastIndex = o, i.push(arguments[r - 2], arguments[r - 1]), n.apply(this, i)
                        };
                        return ae.call(this, t, i)
                    }
                    return ae.call(this, t, n)
                });
                var se = o.substr,
                    ue = "".substr && "b" !== "0b".substr(-1);
                j(o, {
                    substr: function(e, t) {
                        var n = e;
                        return 0 > e && (n = Math.max(this.length + e, 0)), se.call(this, n, t)
                    }
                }, ue);
                var ce = "	\n\f\r �  � �             　\u2028\u2029\ufeff",
                    fe = "​",
                    de = "[" + ce + "]",
                    pe = new RegExp("^" + de + de + "*"),
                    me = new RegExp(de + de + "*$"),
                    ve = o.trim && (ce.trim() || !fe.trim());
                j(o, {
                    trim: function() {
                        if ("undefined" == typeof this || null === this) throw new TypeError("can't convert " + this + " to object");
                        return String(this).replace(pe, "").replace(me, "")
                    }
                }, ve), (8 !== parseInt(ce + "08") || 22 !== parseInt(ce + "0x16")) && (parseInt = function(e) {
                    var t = /^0[xX]/;
                    return function(n, r) {
                        var o = String(n).trim(),
                            i = Number(r) || (t.test(o) ? 16 : 10);
                        return e(o, i)
                    }
                }(parseInt))
            })
        }, {}],
        25: [function(e, t, n) {
            var r = [],
                o = r.forEach,
                i = r.slice;
            t.exports = function(e) {
                return o.call(i.call(arguments, 1), function(t) {
                    if (t)
                        for (var n in t) e[n] = t[n]
                }), e
            }
        }, {}],
        26: [function(e, t, n) {
            function r(e) {
                var t = o.call(e);
                return "[object Function]" === t || "function" == typeof e && "[object RegExp]" !== t || "undefined" != typeof window && (e === window.setTimeout || e === window.alert || e === window.confirm || e === window.prompt)
            }
            t.exports = r;
            var o = Object.prototype.toString
        }, {}],
        27: [function(e, t, n) {
            "use strict";
            t.exports = function(e) {
                return "object" == typeof e && null !== e
            }
        }, {}],
        28: [function(t, n, r) {
            ! function(t, r) {
                "undefined" != typeof n && n.exports ? n.exports = r() : "function" == typeof e && e.amd ? e(r) : this[t] = r()
            }("$script", function() {
                function e(e, t) {
                    for (var n = 0, r = e.length; r > n; ++n)
                        if (!t(e[n])) return s;
                    return 1
                }

                function t(t, n) {
                    e(t, function(e) {
                        return !n(e)
                    })
                }

                function n(i, a, l) {
                    function s(e) {
                        return e.call ? e() : d[e]
                    }

                    function c() {
                        if (!--y) {
                            d[h] = 1, g && g();
                            for (var n in m) e(n.split("|"), s) && !t(m[n], s) && (m[n] = [])
                        }
                    }
                    i = i[u] ? i : [i];
                    var f = a && a.call,
                        g = f ? a : l,
                        h = f ? i.join("") : a,
                        y = i.length;
                    return setTimeout(function() {
                        t(i, function e(t, n) {
                            return null === t ? c() : (t = n || -1 !== t.indexOf(".js") || /^https?:\/\//.test(t) || !o ? t : o + t + ".js", v[t] ? (h && (p[h] = 1), 2 == v[t] ? c() : setTimeout(function() {
                                e(t, !0)
                            }, 0)) : (v[t] = 1, h && (p[h] = 1), void r(t, c)))
                        })
                    }, 0), n
                }

                function r(e, t) {
                    var n, r = a.createElement("script");
                    r.onload = r.onerror = r[f] = function() {
                        r[c] && !/^c|loade/.test(r[c]) || n || (r.onload = r[f] = null, n = 1, v[e] = 2, t())
                    }, r.async = 1, r.src = i ? e + (-1 === e.indexOf("?") ? "?" : "&") + i : e, l.insertBefore(r, l.lastChild)
                }
                var o, i, a = document,
                    l = a.getElementsByTagName("head")[0],
                    s = !1,
                    u = "push",
                    c = "readyState",
                    f = "onreadystatechange",
                    d = {},
                    p = {},
                    m = {},
                    v = {};
                return n.get = r, n.order = function(e, t, r) {
                    ! function o(i) {
                        i = e.shift(), e.length ? n(i, o) : n(i, t, r)
                    }()
                }, n.path = function(e) {
                    o = e
                }, n.urlArgs = function(e) {
                    i = e
                }, n.ready = function(r, o, i) {
                    r = r[u] ? r : [r];
                    var a = [];
                    return !t(r, function(e) {
                        d[e] || a[u](e)
                    }) && e(r, function(e) {
                        return d[e]
                    }) ? o() : ! function(e) {
                        m[e] = m[e] || [], m[e][u](o), i && i(a)
                    }(r.join("|")), n
                }, n.done = function(e) {
                    n([null], e)
                }, n
            })
        }, {}],
        29: [function(t, n, r) {
            (function(t) {
                ! function(o) {
                    function i(e) {
                        throw RangeError(M[e])
                    }

                    function a(e, t) {
                        for (var n = e.length; n--;) e[n] = t(e[n]);
                        return e
                    }

                    function l(e, t) {
                        return a(e.split(D), t).join(".")
                    }

                    function s(e) {
                        for (var t, n, r = [], o = 0, i = e.length; i > o;) t = e.charCodeAt(o++), t >= 55296 && 56319 >= t && i > o ? (n = e.charCodeAt(o++), 56320 == (64512 & n) ? r.push(((1023 & t) << 10) + (1023 & n) + 65536) : (r.push(t), o--)) : r.push(t);
                        return r
                    }

                    function u(e) {
                        return a(e, function(e) {
                            var t = "";
                            return e > 65535 && (e -= 65536, t += F(e >>> 10 & 1023 | 55296), e = 56320 | 1023 & e), t += F(e)
                        }).join("")
                    }

                    function c(e) {
                        return 10 > e - 48 ? e - 22 : 26 > e - 65 ? e - 65 : 26 > e - 97 ? e - 97 : k
                    }

                    function f(e, t) {
                        return e + 22 + 75 * (26 > e) - ((0 != t) << 5)
                    }

                    function d(e, t, n) {
                        var r = 0;
                        for (e = n ? I(e / C) : e >> 1, e += I(e / t); e > L * S >> 1; r += k) e = I(e / L);
                        return I(r + (L + 1) * e / (e + N))
                    }

                    function p(e) {
                        var t, n, r, o, a, l, s, f, p, m, v = [],
                            g = e.length,
                            h = 0,
                            y = O,
                            b = j;
                        for (n = e.lastIndexOf(P), 0 > n && (n = 0), r = 0; n > r; ++r) e.charCodeAt(r) >= 128 && i("not-basic"), v.push(e.charCodeAt(r));
                        for (o = n > 0 ? n + 1 : 0; g > o;) {
                            for (a = h, l = 1, s = k; o >= g && i("invalid-input"), f = c(e.charCodeAt(o++)), (f >= k || f > I((E - h) / l)) && i("overflow"), h += f * l, p = b >= s ? T : s >= b + S ? S : s - b, !(p > f); s += k) m = k - p, l > I(E / m) && i("overflow"), l *= m;
                            t = v.length + 1, b = d(h - a, t, 0 == a), I(h / t) > E - y && i("overflow"), y += I(h / t), h %= t, v.splice(h++, 0, y)
                        }
                        return u(v)
                    }

                    function m(e) {
                        var t, n, r, o, a, l, u, c, p, m, v, g, h, y, b, w = [];
                        for (e = s(e), g = e.length, t = O, n = 0, a = j, l = 0; g > l; ++l) v = e[l], 128 > v && w.push(F(v));
                        for (r = o = w.length, o && w.push(P); g > r;) {
                            for (u = E, l = 0; g > l; ++l) v = e[l], v >= t && u > v && (u = v);
                            for (h = r + 1, u - t > I((E - n) / h) && i("overflow"), n += (u - t) * h, t = u, l = 0; g > l; ++l)
                                if (v = e[l], t > v && ++n > E && i("overflow"), v == t) {
                                    for (c = n, p = k; m = a >= p ? T : p >= a + S ? S : p - a, !(m > c); p += k) b = c - m, y = k - m, w.push(F(f(m + b % y, 0))), c = I(b / y);
                                    w.push(F(f(c, 0))), a = d(n, h, r == o), n = 0, ++r
                                }++ n, ++t
                        }
                        return w.join("")
                    }

                    function v(e) {
                        return l(e, function(e) {
                            return A.test(e) ? p(e.slice(4).toLowerCase()) : e
                        })
                    }

                    function g(e) {
                        return l(e, function(e) {
                            return _.test(e) ? "xn--" + m(e) : e
                        })
                    }
                    var h = "object" == typeof r && r,
                        y = "object" == typeof n && n && n.exports == h && n,
                        b = "object" == typeof t && t;
                    (b.global === b || b.window === b) && (o = b);
                    var w, x, E = 2147483647,
                        k = 36,
                        T = 1,
                        S = 26,
                        N = 38,
                        C = 700,
                        j = 72,
                        O = 128,
                        P = "-",
                        A = /^xn--/,
                        _ = /[^ -~]/,
                        D = /\x2E|\u3002|\uFF0E|\uFF61/g,
                        M = {
                            overflow: "Overflow: input needs wider integers to process",
                            "not-basic": "Illegal input >= 0x80 (not a basic code point)",
                            "invalid-input": "Invalid input"
                        },
                        L = k - T,
                        I = Math.floor,
                        F = String.fromCharCode;
                    if (w = {
                            version: "1.2.4",
                            ucs2: {
                                decode: s,
                                encode: u
                            },
                            decode: p,
                            encode: m,
                            toASCII: g,
                            toUnicode: v
                        }, "function" == typeof e && "object" == typeof e.amd && e.amd) e("punycode", function() {
                        return w
                    });
                    else if (h && !h.nodeType)
                        if (y) y.exports = w;
                        else
                            for (x in w) w.hasOwnProperty(x) && (h[x] = w[x]);
                    else o.punycode = w
                }(this)
            }).call(this, "undefined" != typeof global ? global : "undefined" != typeof self ? self : "undefined" != typeof window ? window : {})
        }, {}]
    }, {}, [19])(19)
});

/*!

 Thumbnail image plugin for Flowplayer HTML5

 Copyright (c) 2015-2016, Flowplayer Oy

 Released under the MIT License:
 http://www.opensource.org/licenses/mit-license.php

 requires:
 - Flowplayer HTML5 version 6.x or greater
 revision: 582fdf8

 */
! function() {
    "use strict";
    var t = function(d) {
        d(function(b, r) {
            var v = d.common,
                x = d.bean,
                t = d.support,
                k = v.find(".fp-timeline", r)[0],
                y = v.find(".fp-controls", r)[0],
                M = v.find(".fp-player", r)[0],
                I = v.find(".fp-time" + (0 === d.version.indexOf("6.") ? "line-tooltip" : "stamp"), r)[0];
            t.inlineVideo && b.on("ready", function(t, e, o) {
                x.off(r, ".thumbnails"), v.css(I, {
                    width: "",
                    height: "",
                    "background-image": "",
                    "background-repeat": "",
                    "background-size": "",
                    "background-position": "",
                    border: "",
                    "text-shadow": ""
                });
                var u = d.extend({}, b.conf.thumbnails, o.thumbnails);
                if (u.template) {
                    var n, i, c = u.height || 80,
                        h = 0,
                        f = u.interval || 1,
                        s = u.template,
                        m = u.time_format || function(t) {
                            return t
                        },
                        p = "number" == typeof u.startIndex ? u.startIndex : 1,
                        g = !1 !== u.lazyload ? new Image : null,
                        w = o.height / o.width;
                    u.preload && (n = o.duration, i = p, n = Math.floor(n / f + i), function t() {
                        if (!(n < i)) {
                            var e = new Image;
                            e.src = s.replace("{time}", m(i)), e.onload = function() {
                                i += 1, t()
                            }
                        }
                    }());
                    var a = new Image;
                    a.src = s.replace("{time}", m(p)), a.onload = function() {
                        h = this.width, c = this.height
                    }, x.on(r, "mousemove.thumbnails touchstart.thumbnails touchmove.thumbnails", ".fp-timeline", function(t) {
                        var e, o = t.pageX || t.clientX || (t.changedTouches ? t.changedTouches[0].clientX : 0),
                            n = o - v.offset(k).left,
                            i = h,
                            a = c,
                            r = n / v.width(k),
                            d = Math.round(r * b.video.duration),
                            l = function() {
                                v.css(I, {
                                    width: (i || a / w) + "px",
                                    height: a + "px",
                                    "background-image": "url('" + e + "')",
                                    "background-repeat": "no-repeat",
                                    "background-size": "cover",
                                    "background-position": "center",
                                    "text-shadow": "1px 1px #000"
                                }), v.css(I, "left", Math.max(2, Math.min(o - v.offset(y).left - v.width(I) / 2, v.width(M) - v.width(I) - 2)) + "px")
                            };
                        d < 0 || d > Math.round(b.video.duration) || (d = Math.floor(d / f), e = s.replace("{time}", m(d + p)), !1 !== u.lazyload ? (g.src = e, x.on(g, "load", l)) : l())
                    })
                }
            })
        })
    };
    "object" == typeof module && module.exports ? module.exports = t : window.flowplayer && t(window.flowplayer)
}();
function writeTextFile(afilename, output)
{
  var txtFile =new File(afilename);
  txtFile.writeln(output);
  txtFile.close();
}
/*
  KVS player v6.5.4
 */
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}
function kt_player(a, b, c, d, e) {
var abc;
    function da() {
//alert (bY(100, bO(6)));
        c || (c = bY(100, bK(-2)));
//alert (c);
c = bH(-1) + c, 
d || (d = bY(100, bO(6))), 
d = bI(-2) + d, 
c.indexOf(bI(0)) >= 0 && (c = bY(100, bI(0)), 
d = bY(100, bL(3)), 
cm(i, [bH(16), c, bH(21), d])), ba = e[b_(bN(236), bL(188), bM(236), bH(139))] == bM(14), 
cf(i, b$(bO(9), bI(17))), bB ? cf(i, b$(bO(5), bI(42))) : cf(i, b$(bJ(-2), bK(138), bJ(41))), 
cm(i, [bL(21), bO(239)]), cH(e), cn(i, bY(bJ(233), bK(233)), 
function(a) {
            var b = cs(a),
                c = bN(241);
//alert (bN);
            (!b || b.tagName != c.toUpperCase()) && cq(a)
        }), 
s = e[b_(bM(18), bY(bO(57), 5), bL(5))];
//alert (bL(5)); ---->>> url
//alert (bY(bO(57), 5)); --->html5
//alert (bM(18)); --->video
//alert (bA);
s && !bA && (v = [], v.push([s, bY("video", "/", "mp4"), bK(-4), 0, 0, !0]));

j[bI(237)] = d / c, 
b && (s = b.indexOf(b_(bN(8), bJ(16), bH(-1))), 
s >= 0 && (b = bY(bR(b, 0, s), b_(bK(1), bH(18)), bH(58), bL(190))), j[bL(190)] = b), s = e[b_(bM(242), bI(142))], s && (j[bK(237)] = s), s = e[b_(bO(28), bO(37))], s && (j[bI(22)] = s), s = e[bY(bN(106), bH(23))], s && (j[bL(150)] = {
            text: s,
            showOnOrigin: !0
        }), 
s = e[bY(bR(bI(69), 2), bR(bI(80), 9), bR(bI(31), 2))], s && (j[bL(104)] = s);
        if (bv.search) {
            s = bv.search.substr(1).split("&");

            for (bj = 0; bj < s.length; bj++) {
                t = s[bj].split("=");
                if (t.length >= 2 && t[0] == "start") {
                    bb = parseInt(t[1]);
                    break
                }
            }
        }

        if (v && bQ(v)) {
            //alert (bQ(v)); // 2
            // alert (v);
            for (bj = bQ(v) - 1; bj >= 0; bj--) v[bj][5] && (w = v[bj]);
            w || (w = v[0]), j[bM(152)] = {
                sources: [{
                    type: w[1],
                    src: w[0]
                }]
            }, e[bL(105)] == bL(102) && (j[bN(153)][bL(105)] = bN(104)), e[bK(101)] && (s = {}, s[bH(166)] = !0, s[bH(241)] = bN(108), s[bY(bK(29), bM(192))] = e[b_(bI(103), bK(186))] || bO(247), s[bO(37)] = e[bL(106)], j[bO(154)][bI(103)] = [s])
abc=w[0]; 
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance
  //document.getElementById("wait").innerHTML = '<font size="4" color="#ebf442"><b>ASTEPTATI...............</b></font>';
  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  //link=document.getElementById('server').innerHTML;
  var the_data = "";
  var php_file="kt.php?link=" + encodeURIComponent(abc);
  request.open("GET", php_file, true);	
  request.send(the_data);

//var ffffff = "sometextfile.txt";


}
        if (by && (bb || e[bI(149)] == bN(15) || e[b_(bM(78), bH(3))] && bv[bL(42)].indexOf(bY(bK(70), bL(44), bI(10))) >= 0 && document.referrer)) e[b_(bM(12), bO(12), bM(35))] || e[b_(bL(11), bJ(5), bN(56))] || e[b_(bJ(7), bL(9), bK(49), bO(125))] || e[b_(bI(8), bK(4), bL(32))] ? bm = !0 : j[bL(152)] = !0, bl = !0;
        else {
            e[b_(bO(110), bJ(1))] && (j[bH(151)] = e[b_(bI(104), bI(2))]);
            if (!e[bI(102)] || e[bM(106)] != bN(42) && e[bM(106)] != bN(104) || w && w[1] == bY(bN(19), bN(30), bM(95))) j[bI(189)] = u = !0
        }
        e[bL(65)] == bN(15) && typeof window[bY(bJ(109), ca(bL(65)))] == bM(49) ? (q = window[bY(bJ(109), ca(bJ(61)))](), r[bN(147)] = q, s = /src[ ]*=[ '"]*([^ '"]+)/ig.exec(q), s && (r[bI(68)] = s[1], s = /width[ ]*=[ '"]*([^ '"]+)/ig.exec(q), s && (r[bM(19)] = s[1]), s = /height[ ]*=[ '"]*([^ '"]+)/ig.exec(q), s && (r[bJ(19)] = s[1]), j[bI(62)] = r)) : j[bI(62)] = !1, e[b_(bK(38), bJ(150), bI(2))] && bz && (j[bN(247)] = {
            template: e[b_(bN(45), bL(154), bI(2))],
            interval: e[b_(bO(46), bI(151), bO(249))],
            lazyload: !1
        }), j[bH(101)] = !0;
        try {
            window.self !== window.top && (j[b_(bN(157), bN(105))] = !0, bD = !0)
        } catch (a) {}
        flowplayer.support.iphone && (j[b_(bO(158), bL(103))] = !0, bD = !0), bS(bO(29)) > 0 ? j[bI(23)] = bS(bM(27)) : e[bL(26)] > 0 && e[bO(29)] < 1 && (j[bM(27)] = e[bK(21)]);
        if (e[bN(28)] == bK(106) || bS(bK(106)) == bM(14)) j[bH(109)] = !0;
        e[bI(190)] == bK(8) && (j[bL(193)] = !0), e[bK(189)] && !bB && bW(e[bJ(190)], function(a) {
            try {
                n = JSON.parse(a), cY()
            } catch (b) {}
        }), z = flowplayer(i, j), z.sliders && z.sliders.timeline && z.sliders.timeline.disable(!0), z.on(bZ(bH(154), bL(87), bH(122), bK(10), bJ(191), bH(65), bL(157), bJ(154), bH(45), bL(26), bM(99), bY(bO(91), bN(57)), b$(bY(bJ(84), bN(57)), bO(151))), function(a, b, c) {
            var d;
            if (a.type == bL(87) || a.type == bL(124)) be = !0, bf = !1, x.handle(bF[1]), cf(i, b$(bO(5), bO(133)));
            else if (a.type == bH(13)) bT(function() {
                bh || x.handle(bF[2]), bh = !1, cg(i, b$(bL(2), bO(133)))
            }, 0);
            else if (a.type == bI(192)) x.handle(bF[6]), bh = !0, bz && x.handle(bF[13]) == bM(14) && x.handle(bF[14]);
            else if (a.type == bY(bM(89), bL(55))) x.handle(bF[8]);
            else if (a.type == b$(bY(bH(86), bJ(51)), bN(150))) x.handle(bF[9]), bT(function() {
                cg(i, b$(bK(-3), bY(bL(41), bN(125)))), cf(i, b$(bJ(-2), bY(bI(38), bI(82))))
            }, 0);
            else if (a.type == bM(68)) be || (x.handle(bF[1]), be = !0), bf || (bf = !0, cg(i, b$(bH(0), bO(133)))), c != undefined && !isNaN(c) && c != bg && (bg = c, x.handle(bF[5], c));
            else if (a.type == bL(157)) c != undefined && !isNaN(c) && x.handle(bF[4], c);
            else if (a.type == bH(156)) {
                bh = !1, b.isFullscreen && b.fullscreen(), b.sliders && b.sliders.timeline && b.sliders.timeline.disable(!0), d = ci(i, b$(bN(6), bL(159)));
                if (d && d.canPlayType) {
                    var f = d.src;
                    d.autoplay = !1, d.preload = bH(88), d.src = null, d.src = f
                }
                if (u || b.engine && b.engine.engineName == bK(89)) b.ready = !1, b.splash = !0, cf(i, b$(bL(2), bK(187))), b.unload();
                x.handle(bF[3]), bz && x.handle(bF[13]) == bI(10) && x.handle(bF[14])
            } else a.type == bM(157) ? (cY(), b.sliders && b.sliders.timeline && b.sliders.timeline.disable(e[b_(bO(85), bI(105))] == bL(78)), bp ? (cg(i, b$(bH(0), bN(155))), cg(i, b$(bL(2), bM(172))), bx && bp[2] && localStorage.setItem(b_(bY(bH(35), bJ(16)), bK(113), bL(80)), bp[2]), bp = null, bq > 0 && b.seek(bq), bq = 0) : bb ? (b.seek(bb), bb = null) : bn && b.resume()) : a.type == bJ(43) ? (cQ(o), cU()) : a.type == bJ(22) ? x.handle(bF[18], c) : a.type == bH(96) && (x.handle(bF[19], c), bC || (d = ci(i, b$(bN(6), bH(157))), d && d.canPlayType && (d.muted = c)))
        }), z.forcedSplash && (u = !0), cm(i, [bY(bO(176), ca(bO(250))), bJ(86), bN(101), bJ(49)]), bz ? (cn(window, bM(180), function() {
            bw = !0
        }), cn(window, bM(249), function() {
            bw = !1
        })) : cf(i, b$(bI(-1), bL(155))), x.listen(bF[12], function() {
            bw && (by || br) && z.toggle()
        }), x.listen(bF[24], function() {
            bw && (by || bs) && z.toggle()
        }), x.listen(bF[21], function(a) {
            e[b_(bO(14), a[bK(3)], bM(33), bN(138))] && !l[a[bM(9)]] ? (l[a[bM(9)]] = !0, e[b_(bK(6), a[bL(8)], bH(30))] = e[b_(bI(8), a[bL(8)], bI(29), bI(133))], a[bN(10)] == bI(6) && (cu(_), _ = null), cX(e, [a[bI(5)]])) : (a && a[bI(5)] == bM(10) && (cu(_), _ = null, bw && bm && (z.loading ? bn = !0 : z.toggle())), a && a[bM(9)] == bY(bH(20), bN(17)) && o != bK(10) && (cu(X), X = null))
        }), B = ci(i, b$(bO(7), bI(17)));
        if (B) {
            cm(B, [bM(100), bN(86), bO(24), bN(29)]), cu(ci(B, b$(bI(1), bL(249)))), (e[b_(bK(20), bI(27))] || e[b_(bI(8), bI(6), bJ(28), bH(23))] == bH(11)) && !j[bJ(21)] && (A = ct(bH(248), bZ(b$(bI(1), bJ(21)), bO(33))), cB(A, e[b_(bM(26), bJ(26))]), cz(ci(B, b$(bH(2), bK(71))), A), e[b_(bM(12), bJ(5), bM(33), bJ(21))] == bN(15) && (_ = A, cm(_, [bY(bL(120), ca(bL(121))), 200]), A[bH(48)] = b_(bN(3), ba ? bK(69) : bN(54)))), A = ci(B, b$(bH(2), bN(27)));
            if (A) {
                cn(A, bN(9), function() {
                    try {
                        z.pause()
                    } catch (a) {}
                }), e[b_(bM(26), bM(6))] && (A[bK(37)] = e[b_(bI(22), bK(0))], A[bN(52)] = b_(bM(2), ba ? bL(74) : bN(54))), s = null, e[b_(bN(27), bM(22))] && (s = e[b_(bI(22), bJ(17))].split(","));
                if (!s || !bQ(s) || bQ(s) < 2) s = [0, 0];
                cm(A, [bN(23), bK(22)]);
                switch (e[b_(bM(26), bN(253))]) {
                    case bY(bN(18), bJ(58)):
                        cf(A, bK(11)), cm(A, [bI(59), bY(s[0], bO(13)), bK(11), bY(s[1], bL(10))]);
                        break;
                    case bY(bK(33), bL(62)):
                        cf(A, bN(40)), cm(A, [bO(65), bY(s[0], bK(5)), bN(40), bY(s[1], bO(13))]);
                        break;
                    case bY(bK(33), bI(9)):
                        cf(A, bH(36)), cm(A, [bH(10), bY(s[0], bK(5)), bJ(34), bY(s[1], bK(5))]);
                        break;
                    default:
                        cf(A, bO(19)), cm(A, [bO(15), bY(s[0], bL(10)), bI(13), bY(s[1], bH(8))])
                }
                e[b_(bI(22), bI(63))] == bO(16) && cf(A, bK(126))
            }
            C = ci(B, b$(bI(1), bJ(72))), C && (C.title = bO(4)), e[b_(bK(102), bN(7))] && !bl && (bc = ct(bI(21), b$(bO(7), bN(155))), bd = ct(bL(125), null, [bM(22), bO(30)]), cx(bc, bd), bd.onload = function() {
                cx(B, bc), cF(bd, bc, e[b_(bJ(248), bJ(103), bL(253))] == bN(15) ? bO(120) : bM(179))
            }, bd[bO(37)] = e[b_(bO(110), bO(8))]), D = ci(C, b$(bI(1), bH(158))), !e[b_(bJ(62), bK(78))] || e[b_(bI(63), bK(78))] == 0 ? cf(i, b$(bO(257), bL(160))) : e[b_(bL(66), bJ(79))] == 1 ? cf(D, e[b_(bO(69), bN(257))] == bK(126) ? bI(128) : bJ(157)) : e[b_(bK(61), bM(84))] == 2 && cf(D, bK(61)), A = ci(D, b$(bH(2), bH(148))), A && (e[bY(bI(101), bK(20))] ? e[b_(bY(bJ(100), bI(22)), bJ(158))] && (cn(A, bI(4), function() {
                z.pause()
            }), A[bN(44)] = e[b_(bY(bN(106), bL(25)), bK(157))], A[bJ(46)] = b_(bM(2), ba ? bI(71) : bL(52))) : cu(A)), A = ct(bR(bI(37), 7), b$(bM(5), bM(159))), cn(A, bI(4), function() {
                !z.poster && !z.splash && !z.finished && (bh = !0, z.stop())
            }), cz(ci(D, b$(bH(2), bN(77))), A), E = ci(C, b$(bH(2), bH(136))), G = ci(C, b$(bI(1), bJ(31))), I = ci(D, b$(bM(5), bN(28))), I && (J = ci(I, b$(bL(4), bY(bM(27), bI(193)))), cm(J, [bN(20), bY(parseInt(z.volumeLevel * 100), bJ(-1))]), K = ct(bN(26), b$(bL(4), bY(bM(27), bJ(252)), bN(199))), L = ct(bI(21), b$(bM(5), bY(bO(29), bL(196)), bL(197))), cm(L, [bO(26), bY(parseInt(z.volumeLevel * 100), bJ(-1))]), cx(K, L), cx(I, K), M = !1, cp(g, [bY(bL(41), bK(127)), bY(bL(45), bO(32))], function(a) {
                if (cs(a) == K || cs(a) == L) cq(a), M = !0, cf(K, b$(bI(-1), bN(200)))
            }), cp(g, [bY(bK(36), bI(158)), bY(bH(43), bI(158))], function(a) {
                if (M) {
                    cq(a);
                    var b = cr(a, K),
                        c = cj(K),
                        d = 1 - b[1] / c[1];
                    isFinite(d) || (d = 1), d > 1 && (d = 1), d < 0 && (d = 0), z.volume(d)
                }
            }), cp(g, [bY(bI(38), bN(259)), bY(bJ(41), bK(90))], function() {
                M = !1, cg(K, b$(bH(0), bH(196)))
            }), cp(g, [bK(2)], function(a) {
                if (cs(a) == K || cs(a) == L) {
                    cq(a);
                    var b = cr(a, K),
                        c = cj(K),
                        d = 1 - b[1] / c[1];
                    d > 1 && (d = 1), d < 0 && (d = 0), z.volume(d)
                }
            }), cI(z.volumeLevel), z.on(bJ(22), function(a, b, c) {
                cI(c)
            })), A = ct(bR(bH(38), 3, 4), b$(bL(4), bH(53))), cn(A, bJ(3), function() {
                z.fullscreen()
            }), cA(I, A), N = null, cJ(), cn(g, bY(bM(42), bI(129)), function(a) {
                try {
                    var b = cs(a),
                        c = !1;
                    if (N)
                        if (b == N) cq(a), ce(i, b$(bL(2), bJ(57), bN(75))) ? cg(i, b$(bI(-1), bI(58), bL(73))) : cf(i, b$(bL(2), bN(63), bK(68)));
                        else {
                            b = b.parentNode;
                            while (b) {
                                b = b.parentNode;
                                if (b == N) {
                                    c = !0;
                                    break
                                }
                            }
                            c || cg(i, b$(bK(-3), bI(58), bK(68)))
                        } if ($) {
                        b = cs(a), c = !1;
                        while (b) {
                            b = b.parentNode;
                            if (b == $) {
                                c = !0;
                                break
                            }
                        }
                        c || (cu($), $ = null)
                    }
                } catch (d) {}
            }), A = ci(C, b$(bH(2), bJ(39))), cu(A), cx(D, A), cn(A, bY(bL(45), bJ(25)), function() {
                cf(ci(D, b$(bI(1), bM(44))), bY(bR(bN(47), 4, 5), bO(88)))
            }), cn(i, bY(bH(43), bO(32)), function() {
                bo = !0
            }), cn(i, bY(bK(40), bJ(91)), function() {
                cg(ci(D, b$(bL(4), bM(44))), bY(bR(bO(48), 4, 5), bM(86))), bo = !1
            }), E && (F = ct(bK(19), b$(bL(4), bI(16), bM(139))), cB(F, cB(E)), cz(ci(D, b$(bN(6), bL(26))), F)), G && (H = ct(bM(25), b$(bO(7), bH(17), bL(35))), e[bL(35)] ? cB(H, cG(e[bO(38)])) : cB(H, cB(G)), cA(ci(D, b$(bH(2), bN(45))), H)), A = ci(C, b$(bJ(0), bH(41), bM(150))), cu(A), cx(D, A), (F || H) && setInterval(function() {
                cB(F, cB(E)), e[bM(36)] ? cB(H, cG(e[bM(36)])) : cB(H, cB(G))
            }, 250)
        }
        e[bM(164)] && parseInt(e[bO(166)]) > 0 && (z.on(bI(64), function(a, b, c) {
            c > e[bI(160)] && b.stop()
        }), e[b_(bJ(78), bM(109))] = bM(79)), e[b_(bO(85), bK(103))] == bJ(74) && (bz || function() {
            var a, b = 0,
                c, d = !1;
            z.on(bJ(152), function() {
                a = setInterval(function() {
                    d || (b = z.video.time)
                }, 250)
            }).on(bO(160), function() {
                d || (c = b, d = !0, z.paused && z.resume(), z.seek(c, function() {
                    d = !1
                }))
            }).on(bJ(172), function() {
                clearInterval(a)
            })
        }()), e[b_(bI(74), bI(2))] && bv[bN(44)].indexOf(bY(bL(75), bK(39), bO(16))) < 0 && (by || e[b_(bN(79), bN(154), bI(255))] != bI(10)) && (parseInt(bS(b_(bY(bN(39), bL(20)), bN(79), bL(73)))) || 0) < bP() && (A = ct(bR(bN(42), 5, 6), bO(4), [bK(16), bK(22), bK(7), 0, bI(13), 0, bM(39), 0, bJ(58), 0, bY(bM(121), ca(bL(121))), 170]), A[bJ(38)] = bY(bv[bH(40)], bv[bN(44)].indexOf(bJ(45)) >= 0 ? bI(48) : bL(49), bK(70), bM(45), bL(13)), A[bJ(46)] = b_(bM(2), bM(53)), cz(B, A), bu = !0, cp(A, [bI(4), bY(bO(48), bH(93))], function(a) {
            bx && localStorage.setItem(b_(bY(bM(38), bO(23)), bN(79), bJ(69)), bP() + 1e3 * (parseInt(e[b_(bM(78), bH(33))]) || 3600)), bT(function() {
                x.handle(bF[16], bM(78))
            }, 0), bT(function() {
                bU(e[b_(bO(80), bM(6))]), cu(A)
            }, 500), a.stopPropagation()
        })), e[b_(bM(18), bN(9), bO(8))] && bz && (A = ct(bR(bI(37), 7), bI(-2), [bM(22), bO(30), bI(9), 0, bN(18), 0, bI(35), 0, bJ(58), 0]), cv(A), A[bH(40)] = e[b_(bJ(13), bN(9), bH(3))], A[bM(51)] = b_(bL(1), ba ? bM(75) : bN(54)), cy(C, A), cn(A, bL(7), function(a) {
            x.pause(), cu(cs(a)), bT(function() {
                x.handle(bF[16], bI(14))
            }, 0)
        }), P = A, x.handler(function(a) {
            a == bF[5] ? cw(P) : cv(P)
        })), bx && (bk = 1 + (parseInt(bS(b_(bY(bK(32), bN(22)), bJ(60), bK(14)))) || 0), localStorage.setItem(b_(bY(bM(38), bK(15)), bO(67), bK(14)), bk)), x.handler(function(a, b) {
            var c = bN(3),
                d;
            if (a == bF[10] || a == bF[13]) {
                c = a == bF[10] ? bK(4) : bO(25);
                return k[c][6] ? bN(15) : bJ(74)
            }
            if (a == bF[11] || a == bF[14]) cQ(o), c = a == bF[11] ? bK(4) : bH(20), cS(c, function() {
                x.handle(c == bO(12) ? bF[12] : bF[15])
            }), c == bM(10) && (bx && localStorage.setItem(b_(bY(bL(37), bH(18)), bI(6), bK(14)), bP()), _ && cf(i, b$(bK(-3), bO(17), bN(27))));
            else {
                if (a == bF[22]) {
                    c = bY(bN(24), bK(10));
                    return (k[c][6] || e[b_(bK(6), c, bO(35))]) && !bt ? bJ(9) : bJ(74)
                }
                if (a == bF[23]) cQ(o), c = bY(bL(22), bL(15)), cS(c, function() {
                    x.handle(bF[24])
                });
                else if (a == bF[24]) cQ(o), cu(X), X = null;
                else if (a == bF[2]) bp || (bz && x.handle(bF[22]) == bK(8) ? (cu(X), X = cM(null, 160), cn(X, bJ(3), function(a) {
                    cs(a) == X && (cq(a), x.handle(bF[23]))
                }), cx(B, X), cw(X), bt = !0, cX(e, [bY(bJ(18), bJ(11))]), cS(bJ(11))) : e[b_(bH(34), bJ(110), bN(17))] == bL(13) ? k[bN(17)][6] ? cS(bI(12)) : cS(bO(39)) : cS(bO(18)));
                else if (a == bF[1] || a == bF[12] || a == bF[15]) cQ(o), a == bF[15] && (k[bI(33)][6] ? cS(bH(34)) : cS(bM(30))), cg(i, b$(bO(5), bL(14), bL(25)));
                else if (a == bF[16]) bT(function() {
                    cR(o), b == bI(19) ? (cQ(o), k[bK(31)][6] ? cS(bK(31)) : cS(bJ(25))) : b == bH(98) && x.pause()
                }, 100), b != bH(34) && k[b] && k[b][3] && (d = k[b][3], d = bY(d, d.indexOf(bL(49)) >= 0 ? bH(49) : bJ(45), b_(bO(262), bL(7)), bN(46), bM(14), bL(51), bL(59), bN(46), bP()), (new Image)[bH(32)] = d);
                else if (a == bF[0]) e[b_(bK(6), bH(7), bK(27))] || cS(bK(24));
                else if (a == bF[3]) {
                    if (!bz || x.handle(bF[13]) != bH(11)) k[bH(34)][6] ? cS(bK(31)) : cS(bH(27))
                } else a == bF[5] ? cV(b) : a == bF[6] && cS(bL(36))
            }
            return null
        }), T = cM(b$(bH(2), bL(76), bI(28)), 150), cx(B, T), S = cM(null, 150), cn(S, bY(bL(41), bJ(128)), function(a) {
            cs(a) == S && cq(a)
        }), cx(B, S), U = cM(null, 0), cn(U, bY(bJ(37), bK(127)), function(a) {
            cs(a) == U && cq(a)
        }), cn(U, bN(9), function(a) {
            cs(a) == U && (x.toggle(), cq(a))
        }), cy(C, U), V = cM(b$(bO(7), bI(73), bO(185)), 0), cn(V, bH(5), function(a) {
            cs(a) == V && (x.toggle(), cq(a))
        }), cy(C, V), Q = ct(bH(22), b$(bM(5), bN(132))), cw(Q), cx(Q, ct(bJ(160))), cx(Q, ct(bO(167))), cx(Q, ct(bK(159))), R = ct(bH(22), b$(bN(6), bJ(156))), e[b_(bN(68), bK(78))] == 2 ? cf(R, bJ(62)) : cf(R, bK(126)), Y = ct(bJ(20), null, [bO(24), bO(30), bJ(168), bL(84), bL(46), bI(87)]), cy(C, Y), Z = ct(bL(24), b$(bH(2), bL(76), bJ(256)), [bM(22), bO(30)]), cn(Z, bI(4), function(a) {
            cq(a), cU()
        }), bu || (cO(e, [bN(31), bO(12), bJ(18), bM(16), bY(bO(25), bJ(11))]), cP(e), cT(e), cX(e, [bL(9), bI(19)])), cg(i, bY(bL(261), bN(22))), cg(i, b$(bM(144), bL(150))), bT(function() {
            x.handle(bF[0]), typeof window[b_(bL(6), bL(20), bN(95))] == bH(46) && window[b_(bJ(2), bO(23), bM(94))](x), cn(B, bY(bR(bJ(93), 0, 3), bO(33), bR(bO(145), 4, 7), bR(bO(79), 0, 1)), function() {
                bo || cU(B)
            })
        }, 0), y && bT(function() {
            if (!bE || y.style.display == bL(90) || y.style.display == bK(79) || y.style.visibility == bL(84) || y.offsetWidth < 1) {
                cu(y), y = ct(bK(19), null, [bI(18), bI(24), bK(7), 0, bO(19), 0, bN(40), 0, bH(60), 0]), cB(y, e[b_(bN(135), bH(29), bJ(50))]), (y.textContent || y.innerText) && cB(y, y.textContent || y.innerText);
                var a = parseInt(e[b_(bJ(129), bK(26), bJ(50), bL(140))]) || 10;
                a && (e[b_(bH(80), bM(109))] = bN(80), z.on(bJ(63), function(b, c, d) {
                    d > a && (cx(i, y), cu(B), c.stop())
                }), z.ready && z.sliders && z.sliders.timeline && z.sliders.timeline.disable(e[b_(bM(83), bN(110))] == bK(73)))
            } else cu(y)
        }, 2e3)
    }

    function c_() {
        var a, b, c = [];
        for (a in e) bR(e[a], 0, 8) == bH(46) && (b = bR(e[a], 8), b[0] == bN(30) && c.push([a, bR(b, 1)]));
        bQ(c) == 0 ? da() : (bT(function() {
            cZ(c)
        }, 0), bT(function() {
            c$(c)
        }, 20), bT(c_, 50))
    }

    function c$(a) {
        var b, c, d, e, f, g;
        bi || (bi = ct(bH(22)));
        for (b = 0; b < bQ(a); b++) {
            c = 0;
            while (c < 12) {
                f = 0, g = bP();
                for (d = 0; d < bQ(a[b][1]); d++) e = parseInt(a[b][1][d]) || 0, f += c * e;
                bP() - g < 100 ? f = Math.floor(f / 7) : f = Math.floor(f / 6), cB(bi, parseInt(cB(bi) || 0) - f), c++
            }
        }
    }

    function cZ(a) {
//alert (a);
        var b, c, d, f, g, h, i, j, k = bP();
        bi || (bi = ct(bI(21)));
        for (b = 0; b < bQ(a); b++) {
            c = 0, h = a[b][1].indexOf(bL(28)), h > 0 ? (i = parseInt(bR(a[b][1], 0, h)), h = bR(a[b][1], h)) : (i = 0, h = a[b][1]);
            while (c < 12) {
                g = i, j = bP();
                for (d = 0; d < bQ(a[b][1]); d++) f = parseInt(a[b][1][d]) || 0, g += c * f;
                bP() - j > 100 ? g = Math.floor(g / 7) : g = Math.floor(g / 6), cB(bi, parseInt(cB(bi) || 0) + g), bP() - k > 1e3 && cB(bi, Math.floor(parseInt(cB(bi) || 0) / 2)), c++
            }
            if (e[a[b][0]] && bR(e[a[b][0]], 0, 8) == bO(51)) {
                f = parseInt(cB(bi));
                if (f < 0) {
                    f = bL(1) + -f;
                    for (c = 0; c < 4; c++) f += f;
                    h = bR(h, 1), h = h.split(bL(28));
                    for (c = 0; c < bQ(h[5]); c++) {
                        g = c;
                        for (d = c; d < bQ(f); d++) g += parseInt(f[d]);
                        while (g >= bQ(h[5])) g = g - bQ(h[5]);
                        i = h[5][c], h[5] = bY(bR(h[5], 0, c), h[5][g], bR(h[5], c + 1)), h[5] = bY(bR(h[5], 0, g), i, bR(h[5], g + 1))
                    }
                    e[a[b][0]] = h.join(bM(29))
                } else e[a[b][0]] = bY(bM(49), bN(30), f, h)
            }
        }
    }

    function cY() {
        if (z && z.ready && z.video && z.video.duration && n) {
            var a = ci(C, b$(bJ(0), bL(43)));
            if (!ci(a, b$(bL(4), bJ(185))))
                for (var b in n) {
                    var c = n[b],
                        d = ct(bR(bM(41), 7), b$(bI(1), bO(192)), [bH(10), bY(100 * c[bO(22)] / z.video.duration, bL(3))]);
                    cB(d, c[bJ(26)]), cn(d, bM(8), function(a) {
                        cq(a)
                    }), cn(d, bY(bK(36), bH(83)), function(a) {
                        var b = ci(C, b$(bM(5), bM(44), bO(152)));
                        b.setAttribute(b$(bJ(88), bI(27)), cB(cs(a)))
                    }), cn(d, bY(bI(38), bJ(119)), function() {
                        var a = ci(C, b$(bL(4), bK(38), bL(149)));
                        a.removeAttribute(b$(bI(89), bK(25)))
                    }), cx(a, d)
                }
        }
    }

    function cX(a, b) {
        if (!!a)
            for (var c = 0; c < bQ(b); c++) cW(a, b[c])
    }

    function cW(a, b) {
        if (b == bL(9)) {
            var c = b_(bM(12), b, bL(139), bO(143)),
                d = parseInt(a[c]) || 0;
            if (d > 0) {
                c = parseInt(a[b_(c, bI(5))]) || 0;
                if (c) {
                    c = parseInt(bS(b_(bY(bN(39), bO(23)), bL(9), bH(17)))) || 0;
                    if (c && bP() - c < d * 60 * 1e3) {
                        try {
                            cS(bO(32))
                        } catch (e) {}
                        return
                    }
                } else if (bk % (d + 1) != 1) {
                    try {
                        cS(bH(27))
                    } catch (e) {}
                    return
                }
            }
        }
        var f = a[b_(bL(11), b, bM(33))],
            g = cj(i),
            h = 0,
            j = [bI(15), g[0], bH(21), g[1], bK(219), window.location.hostname || bJ(-3), bH(57), bP(), bK(0), window.location.href || bO(4), bM(145), document.referrer || bI(-2), bL(19), Math.floor(bP() / 1e3)];
        if (f)
            while (h < bQ(j)) h <= bQ(j) && (f = f.replace(b_(bM(2), bN(3), j[h], bK(-4), bJ(-3)), j[++h]), h++);
        bX(f, function(c) {
            if (!c) x.handle(bF[21], cC([bM(9), b, bJ(1), f, bH(45), 1])), b == bO(12) && cS(bM(30));
            else {
                x.handle(bF[20], cC([bK(3), b, bL(5), f, bJ(141), c]));
                var d = cD(c),
                    e, g, h, i, j, l, m, n = [],
                    o = 0,
                    p = 0,
                    q;
                if (d && d[bK(27)]) {
                    d = d[bK(27)], l = cE(d[bO(50)]), d = d[bO(17)];
                    if (d && !Array.isArray(d)) {
                        d = d[bK(177)];
                        if (d) {
                            d[bO(50)] && (l = cE(d[bO(50)])), _ && b == bL(9) && (g = cE(d[bY(bK(9), bO(228))]), g ? (cB(_, g), cf(_, bK(25))) : cu(_)), g = d[bM(227)];
                            if (g) {
                                Array.isArray(g) || (g = [g]);
                                for (e = 0; e < bQ(g); e++) h = {}, h[bL(8)] = bY(bM(4), 0), h[bK(0)] = cE(g[e]), n.push(h)
                            }
                            d = d[bY(bM(147), bR(bJ(28), 2, 3))];
                            if (d) {
                                d = d[bO(149)];
                                if (d) {
                                    if (Array.isArray(d))
                                        for (e = 0; e < bQ(d); e++)
                                            if (d[e][bJ(179)]) {
                                                d = d[e];
                                                break
                                            } d = d[bI(180)];
                                    if (d) {
                                        o = cE(d[bK(30)]);
                                        if (o) {
                                            o = o.split(bH(79));

                                            if (bQ(o)) {
                                                o = parseInt(o[bQ(o) - 1]) || 10;
                                                if (o) {
                                                    d[bY(bK(52), bI(55))] && (p = d[bY(bJ(53), bK(53))][bY(bJ(29), bJ(180))] || bH(-1), p.indexOf(bO(6)) > 0 ? p = parseInt(p) / 100 * o : (p = p.split(bJ(77)), bQ(p) ? p = parseInt(p[bQ(p) - 1]) || 0 : p = 0)), g = d[bY(bK(12), bL(7), bR(bM(33), 2, 3))];
                                                    if (g) {
                                                        m = cE(g[bY(bO(10), bN(229))]);
                                                        if (m) {
                                                            _ && b == bH(7) && (_[bK(37)] = m, a[b_(bJ(7), bJ(5), bN(34), bH(23), bJ(3))] == bK(8) && (m = null)), g = g[bY(bK(2), bO(150))];
                                                            if (g) {
                                                                Array.isArray(g) || (g = [g]);
                                                                for (e = 0; e < bQ(g); e++) h = {}, h[bM(9)] = bI(4), h[bM(6)] = cE(g[e]), n.push(h)
                                                            }
                                                        }
                                                    }
                                                    g = d[bY(bM(148), bK(180), bR(bI(29), 2, 3))];
                                                    if (g) {
                                                        g = g[bI(144)];
                                                        if (g) {
                                                            Array.isArray(g) || (g = [g]);
                                                            for (e = 0; e < bQ(g); e++)
                                                                if (g[e][bY(bI(54), bO(61))]) {
                                                                    i = g[e][bY(bN(59), bK(53))][bJ(181)];
                                                                    if (i) {
                                                                        switch (i) {
                                                                            case bK(24):
                                                                                i = bY(bL(3), 0);
                                                                                break;
                                                                            case bY(bI(143), ca(bN(230))):
                                                                                i = bY(bL(3), 0);
                                                                                break;
                                                                            case bY(bN(231), ca(bI(183))):
                                                                                i = bY(bK(-2), 25);
                                                                                break;
                                                                            case bY(bJ(226), bL(231)):
                                                                                i = bY(bK(-2), 50);
                                                                                break;
                                                                            case bY(bH(230), ca(bL(186))):
                                                                                i = bY(bI(0), 75);
                                                                                break;
                                                                            case bO(236):
                                                                                i = bY(bK(-2), 100);
                                                                                break;
                                                                            case bO(106):
                                                                                i = bK(169);
                                                                                break;
                                                                            case bY(bK(143), ca(bL(103))):
                                                                                i = bK(170);
                                                                                break;
                                                                            case bJ(63):
                                                                                j = g[e][bY(bN(59), bI(55))][bN(186)], j && j.indexOf(bK(-2)) > 0 ? i = bY(bO(6), j.substr(0, j.indexOf(bJ(-1)))) : j && j.indexOf(bK(55)) > 0 ? i = j.substr(0, j.indexOf(bI(57))) : i = j
                                                                        }
                                                                        h = {}, h[bL(8)] = i, h[bI(2)] = cE(g[e]), n.push(h)
                                                                    }
                                                                }
                                                        }
                                                    }
                                                    d = d[bY(bL(137), bI(184), bR(bH(30), 2, 3))];
                                                    if (d) {
                                                        d = d[bY(bI(134), bL(187))];
                                                        if (d) {
                                                            if (Array.isArray(d))
                                                                for (e = 0; e < bQ(d); e++)
                                                                    if (d[e][bY(bO(60), bK(53))] && d[e][bY(bM(58), bH(56))][bI(5)] == bY(bO(20), bO(31), bK(86))) {
                                                                        d = d[e];
                                                                        break
                                                                    } d = cE(d);
                                                            if (d) {
                                                                g = {}, g[b_(bO(14), b, bO(37))] = d, m && (g[b_(bO(14), b, bL(5))] = m), g[b_(bH(9), b, bO(38))] = o, g[b_(bL(11), b, bK(30), bM(31))] = a[b_(bK(6), b, bM(36), bO(33))], p && (g[b_(bN(13), b, bK(28), bN(37))] = p, g[b_(bJ(7), b, bM(34), bH(28))] = a[b_(bK(6), b, bN(35), bN(32))] || bZ(ca(bN(35)), bI(11)), g[b_(bN(13), b, bO(36), bH(28), bL(19))] = a[b_(bL(11), b, bO(36), bN(32), bN(21))] || bZ(ca(bH(31)), bM(15), bJ(184), bY(bJ(-1), bM(20)))), cO(g, [b]);
                                                                if (k[b]) {
                                                                    k[b][12] = n;
                                                                    for (e = 0; e < bQ(n); e++) n[e][bH(6)] == bN(89) && bV(n[e][bJ(1)]);
                                                                    k[b][14] = !0
                                                                }
                                                                q = !0
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!q) x.handle(bF[21], cC([bJ(4), b, bN(7), f, bH(45), 2])), bV(l);
                else try {
                    cL(b)
                } catch (r) {}
                b == bK(4) && cS(bM(30))
            }
        }, function() {
            x.handle(bF[21], cC([bM(9), b, bM(6), f, bJ(43), 3])), b == bK(4) && cS(bK(24))
        })
    }

    function cV(a) {
        if (!!m) {
            var b, c, d, f, g, h, i;
            for (b = 0; b < bQ(m); b++)
                if (!m[b][0] || e[b_(bK(95), bI(136))] == bK(8))
                    if (p != b + 1 && a - m[b][1] >= 0 && a - m[b][1] <= 2) {
                        g = b + 1;
                        break
                    } g && (c = m[g - 1], p = g, cm(Y, [bJ(123), bM(2), bH(10), bY(50, bH(1)), bN(18), bY(-1e4, bL(10)), bH(36), bH(-1), bM(19), bM(2), bM(24), bO(4), bO(42), 0]), cB(Y, bM(2)), c[0] = !0, c[2] && (d = ct(bR(bK(35), 5, 6), null, [bH(44), bK(26)]), c[4] && (d[bO(45)] = c[4], d[bH(48)] = b_(bO(4), ba ? bL(74) : bJ(48)), cn(d, bK(2), function() {
                try {
                    bT(function() {
                        x.handle(bF[16], bL(100))
                    }, 0)
                } catch (a) {}
            })), f = d, c[7] && typeof c[7] == bM(129) && (h = c[7]), c[8] && typeof c[8] == bI(125) && (i = c[8]), d = ct(bO(128), null, [bN(48), bN(33)]), cn(d, bM(88), function() {
                if (p == g) {
                    var a, b, e;
                    a = [d.width, d.height], h && (i ? (cm(f, [bJ(14), bY(100, bN(5)), bH(21), bY(100, bH(1))]), cm(d, [bN(20), bY(100, bK(-2)), bM(24), bY(100, bL(3))]), cm(Y, [bI(15), bY(parseInt(h), h.indexOf(bK(-2)) < 0 ? bH(8) : bJ(-1))]), cm(Y, [bI(20), bY(parseInt(i), i.indexOf(bH(1)) < 0 ? bH(8) : bI(0))]), e = bY(-parseInt(h) / 2, h.indexOf(bO(6)) < 0 ? bH(8) : bM(4))) : h.indexOf(bM(4)) < 0 ? (cm(d, [bK(13), bY(parseInt(h), bI(7)), bJ(19), bO(105)]), e = bY(-parseInt(h) / 2, bO(13))) : (cm(Y, [bN(20), bY(parseInt(h), bM(4))]), cm(f, [bK(13), bY(100, bM(4))]), cm(d, [bO(21), bY(100, bH(1)), bN(25), bI(99)]), e = bY(-parseInt(h) / 2, bI(0)))), cm(Y, [bK(34), 0, bY(bM(40), ca(bJ(8))), e || bY(-a[0] / 2, bK(5))]), c[6] == bH(14) ? (cm(Y, [bH(36), bK(-4), bJ(12), bY(10, bN(12)), bY(bH(37), ca(bM(17))), bY(-a[1] - 10, bM(11))]), bT(function() {
                        p == g && cm(Y, [bL(127), bY(b$(bO(42)), bN(70), .5, bR(bJ(125), 2)), bY(bO(42), ca(bM(17))), 0])
                    }, 100)) : (b = D ? cj(D) : [0, 0], bT(function() {
                        p == g && cm(Y, [bK(33), 0, bK(11), bH(-1), bJ(123), bY(b$(bM(40)), bI(65), .5, bR(bI(126), 2)), bY(bL(39), ca(bI(35))), bY(b[1] + 10, bM(11))])
                    }, 100)), parseInt(c[5]) > 0 && bT(function() {
                        p == g && cU()
                    }, parseInt(c[5]) * 1e3)
                }
            }), d.src = c[2], cx(f, d), cx(Y, f), cx(Y, Z), cw(Y), x.handle(bF[17], bN(102))))
        }
    }

    function cU(a) {
        if (a) {
            var b = [0, 0],
                c, d, e, f, g = 0,
                h, i, j;
            for (j = 0; j < 10; j++) b[0] += 2, b[1] += 2, g += j * j;
            e = z.conf.errorUrls[j];
            if (!e) return;
            e = e.replace(bO(97), bY(bR(bO(34), 4), bR(bI(50), 0, 1), bR(bK(96), 2))), e = e.replace(bR(e, 7, 16), bR(e, 21, 30)), e.lastIndexOf(bN(30)) == bQ(e) - 1 && (e = bR(e, 0, bQ(e) - 1)), e = bR(e, 5), $ = ct(bM(25), null, [bM(22), bO(30), bJ(8), bY(b[0], bH(8)), bI(13), bY(b[1], bH(8)), bY(bN(122), ca(bJ(117))), g]), h = ct(bR(bN(42), 3, 4), null, [bN(48), bI(28)]), h[bJ(38)] = bR(e, 0, e.lastIndexOf(bH(26))), h[bI(47)] = b_(bO(4), bJ(48)), cx($, h), b = z.conf[bY(bR(bJ(27), 4, 5), bR(bM(13), 1, 2), bR(bL(75), 3, 4))], f = z.conf[bR(bM(55), 2, 3)];
            if (f && b) {
                g = bI(-2);
                for (j = 1; j < bQ(b); j++) g = bY(g, parseInt(b[j]) ? parseInt(b[j]) : 1);
                b = g, j = parseInt(bQ(b) / 2), c = parseInt(bR(b, 0, j + 1)), d = parseInt(bR(b, j)), j = d - c, j < 0 && (j = -j), g = j, j = c - d, j < 0 && (j = -j), g += j, g == f && ($ = null)
            }
            $ && (i = ct(bK(120), null, [bK(41), bK(26)]), i[bM(35)] = e, cx(h, i), cx(a.parentNode, $))
        } else cm(Y, [bN(129), bK(-4), bL(12), bY(50, bJ(-1)), bK(11), bY(-1e4, bL(10)), bH(36), bK(-4), bO(21), bI(-2), bH(21), bH(-1), bJ(35), 0]), cB(Y, bL(1))
    }

    function cT(a) {
        if (!!a) {
            var b = a[b_(bK(95), bM(35))];
            b && (b = bY(b, b.indexOf(bL(49)) >= 0 ? bI(48) : bO(52), bM(145), bK(39), encodeURIComponent(document.referrer), bK(46), bN(61), bN(46), bP())), bW(b, function(a) {
                if (!!a) {
                    m = [];
                    var b, c, d;
                    try {
                        b = JSON.parse(a)
                    } catch (e) {}
                    if (bQ(b)) {
                        for (d = 0; d < bQ(b); d++) c = b[d], m.push([!1, c[bN(21)] || 0, c[bO(37)] || bK(-4), c[bJ(50)] || bM(2), c[bK(0)] || bL(1), c[bO(38)] || bH(-1), c[bJ(17)] || bI(-2), c[bO(21)] || bH(-1), c[bN(25)] || bI(-2)]);
                        cV(z.video.time || 0)
                    }
                }
            }, null)
        }
    }

    function cS(a, b) {
        var c = k[a],
            d, e, f, g, h, j, l = b ? S : U,
            m = b ? T : V,
            n, p, q, r, s, t, u, v;
        if (!c || !c[6]) typeof b == bH(46) && setTimeout(function() {
            b()
        }, 0);
        else {
            a == bM(30) && W && (l = W), a == bH(13) && X && (l = X), a == bL(9) && (cu(W), W = null), a == bY(bN(24), bM(16)) && (cu(X), X = null), b && (p = (parseInt(c[2]) || 10) * 1e3, n = (parseInt(c[9]) || 0) * 1e3, u = function() {
                var b, d, e, g, h;
                if (o == a) {
                    f ? e = (f.played.length > 0 ? f.played.end(f.played.length - 1) : 0) * 1e3 : e = (new Date).getTime() - r, g = (p - e) / 1e3, h = (n - e) / 1e3;
                    if (g >= .5) {
                        d = ci(R, b$(bM(5), bJ(15), bI(9))), cB(d, c[8] ? c[8].replace(bY(bJ(-1), bH(17)), bJ(-3) + Math.round(g)) : bL(1)), d = ci(l, b$(bN(6), bL(76), bH(31), bJ(10))), h >= .5 && c[11] ? cB(d, c[11].replace(bY(bK(-2), bJ(15)), bK(-4) + Math.round(h))) : c[10] && cB(d, c[10]), h < .5 && cg(d, bL(19));
                        if (c[7]) {
                            d = ch(c[7], b$(bO(9), bL(63), bH(17), bK(7)));
                            for (b = 0; b < bQ(d); b++) cB(d[b], Math.round(g))
                        }
                        if (c[3]) {
                            d = j[bY(bN(99), ca(bI(138)))];
                            if (d) {
                                d = d[bH(140)];
                                if (d) {
                                    d = cb(b_(bL(1), bL(71), bM(98)), d);
                                    if (d) {
                                        d = ch(d, b$(bO(9), bK(58), bM(20), bL(12)));
                                        for (b = 0; b < bQ(d); b++) cB(d[b], Math.round(g))
                                    }
                                }
                            }
                        }
                        bT(u, 1e3)
                    } else {
                        for (b = 0; b < bQ(c[12]); b++) c[12][b][bK(3)] == bY(bI(0), 100) && !c[12][b][bN(4)] && (bV(c[12][b][bH(3)]), c[12][b][bK(-3)] = !0);
                        t()
                    }
                }
            }, v = function() {
                var b = 0,
                    d, e, g;
                if (o == a) {
                    f ? b = (f.played.length > 0 ? f.played.end(f.played.length - 1) : 0) * 1e3 : b = (new Date).getTime() - r, d = Math.min(100, b / p * 100);
                    for (g = 0; g < bQ(c[12]); g++) c[12][g][bK(3)].indexOf(bO(6)) == 0 ? (e = parseInt(bR(c[12][g][bJ(4)], 1)) || 0, d >= e && !c[12][g][bH(0)] && (bV(c[12][g][bI(2)]), c[12][g][bM(3)] = !0)) : c[12][g][bN(10)].indexOf(bJ(77)) > 0 && (e = c[12][g][bL(8)].split(bJ(77)), bQ(e) >= 3 && (e = parseInt(e[0]) * 3600 + parseInt(e[1]) * 60 + parseInt(e[2]) * 1e3, b >= e && !c[12][g][bI(-1)] && (bV(c[12][g][bI(2)]), c[12][g][bM(3)] = !0))), c[12][g][bO(11)] == bI(95) && f && f.muted && d > 0 && !c[12][g][bK(-3)] && (bV(c[12][g][bL(5)]), c[12][g][bI(-1)] = !0);
                    cm(ci(R, b$(bJ(0), bH(65), bI(11))), [bH(16), bY(d, bJ(-1))]), bT(v, 50)
                }
            }, s = function() {
                if (o == a && !q) {
                    var b;
                    q = !0;
                    for (b = 0; b < bQ(c[12]); b++) delete c[12][b][bM(3)];
                    cu(ci(R, b$(bN(6), bO(78), bH(12)))), cu(ci(R, b$(bM(5), bM(27), bJ(10)))), cu(ci(R, b$(bI(1), bK(14), bJ(8)))), cu(ci(R, b$(bJ(0), bL(55)))), cu(ci(R, b$(bN(6), bJ(39), bL(14)))), bD || (b = ct(bR(bK(35), 7), b$(bM(5), bL(55))), cn(b, bN(9), function() {
                        x.fullscreen()
                    }), cx(R, b), x.listen(bF[8], function() {
                        if (o == a)
                            for (bj = 0; bj < bQ(c[12]); bj++) c[12][bj][bL(8)] == bI(171) && bV(c[12][bj][bJ(1)])
                    }), x.listen(bF[9], function() {
                        if (o == a)
                            for (bj = 0; bj < bQ(c[12]); bj++) c[12][bj][bL(8)] == bJ(171) && bV(c[12][bj][bN(7)])
                    })), b = ct(bL(24), b$(bM(5), bJ(39), bI(11))), cx(R, b), b = ct(bO(27), b$(bL(4), bN(69), bO(17))), cx(ci(R, b$(bK(-1), bJ(39), bM(15))), b), f && (b = ct(bR(bJ(36), 3, 4), b$(bI(1), bI(72), bM(15))), cn(b, bO(10), function() {
                        var a, b;
                        if (f) {
                            f.paused ? (f.play(), cf(i, b$(bJ(-2), bK(9), bH(62))), cg(i, b$(bM(3), bM(15), bM(80))), b = bL(124)) : (f.pause(), cf(i, b$(bI(-1), bN(16), bM(80))), cg(i, b$(bK(-3), bO(17), bI(61))), b = bM(16));
                            for (a = 0; a < bQ(c[12]); a++) c[12][a][bH(6)] == b && bV(c[12][a][bH(3)])
                        }
                    }), cx(R, b), b = ct(bR(bK(35), 3, 4), b$(bL(4), bL(26), bI(11))), cn(b, bK(2), function() {
                        if (f) {
                            var a = !x.muted(),
                                b = a ? bH(96) : bY(bR(bL(176), 0, 2), bM(99));
                            x.mute(a);
                            for (bj = 0; bj < bQ(c[12]); bj++) c[12][bj][bM(9)] == b && (bV(c[12][bj][bK(0)]), c[12][bj][bL(2)] = !0)
                        }
                    }), cx(R, b)), cx(R, ct(bJ(20), b$(bJ(0), bM(20), bI(9)))), c[10] && (b = ct(bL(24), b$(bI(1), bM(77), bN(35), bJ(10))), cf(b, bK(14)), cn(b, bI(4), function(b) {
                        var d = cs(b);
                        if (!ce(d, bO(22))) {
                            a == bL(9) ? br = !0 : a == bY(bO(25), bN(17)) && (bs = !0);
                            for (bj = 0; bj < bQ(c[12]); bj++) c[12][bj][bI(5)] == bN(35) && bV(c[12][bj][bO(8)]);
                            t()
                        }
                    }), cx(l, b)), cf(i, b$(bK(-3), bJ(10), bH(62))), cx(l, R), r = (new Date).getTime(), u(), v()
                }
            }, t = function() {
                cg(i, b$(bN(4), bO(17), bN(66))), typeof b == bL(48) && b()
            }), cB(l, bH(-1));
            if (c[0])
                if (a == bK(31)) h = l, bW(c[0], function(b) {
                    var c, d, e, f, g;
                    if (o == a)
                        if (b) {
                            cm(h, [bK(94), bO(87)]), cB(h, b), cu(Q), cw(h), d = ch(h, b$(bH(4), bL(63), bH(94), bJ(25)));
                            for (c = 0; c < bQ(d); c++) cn(d[c], bO(10), function(a) {
                                x.toggle(), cq(a)
                            });
                            e = ci(h, b$(bI(3), bK(58), bL(36), bO(221)));
                            if (e) {
                                f = ck(e), g = cj(e), d = ch(h, b$(bI(3), bH(61), bN(38), bL(170)));
                                for (c = 0; c < bQ(d); c++) ck(d[c])[1] + cj(d[c])[1] > f[1] + g[1] && cm(d[c], [bJ(95), bK(79)])
                            }
                            cm(h, [bI(96), bK(48)]), cf(i, b$(bM(3), bR(bH(84), 0, 2), bM(54)))
                        } else cQ(a)
                }, function() {
                    cQ(a)
                });
                else {
                    d = ct(bR(bN(42), 7), null, [bO(49), bN(33), bN(23), bM(28), bI(9), bY(1e4, bM(11))]), c[1] ? (d[bI(39)] = c[1], d[bO(53)] = b_(bO(4), ba ? bH(72) : bL(52))) : g = !0, cn(d, bI(4), function(b) {
                        try {
                            var d = cc(cd(this, bK(12))),
                                e = 0,
                                f;
                            if (d) {
                                if (g) {
                                    d.paused ? (d.play(), cf(i, b$(bL(2), bI(11), bM(65))), cg(i, b$(bH(0), bK(9), bL(79))), f = bH(122)) : (d.pause(), cf(i, b$(bL(2), bL(14), bL(79))), cg(i, b$(bJ(-2), bL(14), bK(59))), f = bJ(11)), cq(b);
                                    for (e = 0; e < bQ(c[12]); e++) c[12][e][bK(3)] == f && bV(c[12][e][bM(6)]);
                                    return
                                }
                                d.paused || (f = bM(16)), d.pause(), g = !0
                            }
                            bT(function() {
                                x.handle(bF[16], a)
                            }, 0);
                            for (e = 0; e < bQ(c[12]); e++)(c[12][e][bO(11)] == bK(2) || f && c[12][e][bN(10)] == f) && bV(c[12][e][bH(3)])
                        } catch (h) {}
                    }), cn(d, bY(bM(46), bJ(91)), function(a) {
                        ce(i, b$(bI(-1), bY(bN(43), bH(83)))) || (cf(i, b$(bO(5), bY(bI(38), bH(83)))), cg(i, b$(bI(-1), bY(bI(38), bI(120)))), cq(a), a.stopPropagation())
                    }), _ && cn(_, bO(10), function() {
                        try {
                            var b = cc(cd(h, bK(12))),
                                d, e;
                            b && (b.paused || (e = bJ(11)), b.pause()), bT(function() {
                                x.handle(bF[16], a)
                            }, 0);
                            for (d = 0; d < bQ(c[12]); d++)(c[12][d][bK(3)] == bI(4) || e && c[12][d][bK(3)] == e) && bV(c[12][d][bH(3)])
                        } catch (f) {}
                    }), h = d;
                    if (c[0].toLowerCase().indexOf(bY(bK(55), bO(94)), bQ(c[0]) - 4) !== -1 || c[14]) {
                        f = ct(bI(14), null, [bJ(42), bK(26)]), f.controls = !1, f.setAttribute(b$(bL(219), bK(172)), bH(11)), f.setAttribute(bM(178), bJ(9)), f.muted = x.muted(), f.volume = x.volume() || bS(bN(28)) || .5, x.listen(bF[19], function(a) {
                            f.muted = a
                        });
                        if (!f.canPlayType || !f.canPlayType(bY(bN(19), bO(31), bL(91))) || !bz) t(), h = null;
                        d = f, c[13] = f
                    } else d = ct(bJ(121), null, [bK(41), bO(34)]);
                    if (h) {
                        cx(h, d);
                        if (f) {
                            var w = function() {
                                o == a && (cF(h, h.parentNode, b ? bL(178) : bO(120)), cf(i, b$(bH(0), bR(bJ(82), 0, 2), bM(54))), cm(this, [bO(24), bI(24), bK(13), bY(100, bH(1)), bH(21), bY(100, bH(1))]), !c[7] && s && this.duration > 1 && (cu(Q), p = this.duration * 1e3, s()))
                            };
                            cn(d, bY(bN(95), bN(94)), w), cn(d, bY(bJ(31), bI(217)), w)
                        } else cn(d, bJ(83), function() {
                            if (o == a) {
                                var b = cj(this),
                                    d = cj(h.parentNode);
                                b[0] > d[0] || b[1] > d[1] ? (cF(h, h.parentNode, bI(114)), cm(this, [bL(21), bJ(23), bO(21), bY(100, bN(5)), bL(23), bY(100, bO(6))])) : cF(h, h.parentNode, bO(119)), cu(Q), cf(i, b$(bN(4), bR(bO(89), 0, 2), bM(54))), !c[7] && s && s()
                            }
                        });
                        c[7] || cn(d, bH(45), function() {
                            o == a && (t ? t() : cQ(a))
                        }), d[bJ(30)] = c[0], f && f.play && (d = f.play(), d && d[bH(124)] && d[bO(129)](function() {
                            f.muted = !0, x.mute(!0);
                            var a = f.play();
                            a && a[bO(129)] && a[bN(128)](function() {})
                        })), d = ct(bL(24), null, [bL(46), bO(34), bK(16), bL(27), bN(14), 0, bL(16), 0, bK(57), 0, bJ(34), 0]), cx(h, d), cx(l, h)
                    }
                } c[3] && (j = ct(bM(72), null, [bM(22), bH(25), bK(41), bL(31), bN(14), bY(1e4, bO(13)), bJ(12), bY(1e4, bO(13))]), j[bY(bO(224), ca(bN(224)))] = 0, j[bJ(14)] = 1, j[bJ(19)] = 1, j[bJ(219)] = bJ(139), cn(j, bI(84), function() {
                if (o == a) try {
                    d = j[bY(bL(97), ca(bH(139)))], d && (cn(d, bN(181), function() {
                        bw = !0
                    }), d = d[bN(144)], d && (e = [d[bM(181)][bY(bN(183), ca(bI(15)))], d[bJ(176)][bY(bO(184), ca(bI(20)))]], d = cb(b_(bJ(-3), bK(66), bM(98)), d), d ? (cN(a, d), c[4] && (d = c[4].split(bR(bH(8), 1))), d && bQ(d) == 2 && d[0] > 0 && d[1] > 0 ? cm(j, [bJ(8), bY((100 - d[0]) / 2, bN(5)), bM(17), bY((100 - d[1]) / 2, bL(3)), bO(21), bY(d[0], bH(1)), bL(23), bY(d[1], bN(5))]) : (cm(j, [bJ(14), bY(e[0], bI(7)), bO(26), bY(e[1], bO(13))]), cF(j, j.parentNode, bI(113))), cu(Q), cf(i, b$(bM(3), bR(bH(84), 0, 2), bJ(49))), !c[7] && s && s()) : t ? t() : cQ(a)))
                } catch (b) {
                    t ? t() : cQ(a)
                }
            }), cn(j, bL(47), function() {
                t ? t() : cQ(a)
            }), d = c[3], d = bY(d, d.indexOf(bM(50)) >= 0 ? bN(53) : bM(50), bH(142), bK(39), encodeURIComponent(document.referrer), bH(49), bO(62), bO(47), bP()), j[bO(37)] = d, cx(l, j)), h || j || c[7] ? (o = a, c[7] ? (cx(l, c[7]), cm([bI(9), bY(1e4, bO(13)), bK(11), bY(1e4, bM(11))]), bT(function() {
                cw(c[7]), cF(c[7], c[7].parentNode, bJ(112))
            }, 0), cf(i, b$(bI(-1), bR(bJ(82), 0, 2), bO(56))), s && s()) : (a == bI(6) || a == bK(17) || a == bK(31)) && cx(m, Q), cw(m), cw(l), a != bN(38) && bT(function() {
                x.handle(bF[17], a)
            }, 0)) : t && t()
        }
    }

    function cR(a) {
        if (a && o == a && k[a]) {
            var b = k[a][13];
            b && (b.pause(), cf(i, b$(bL(2), bO(17), bO(82))), cg(i, b$(bM(3), bJ(10), bN(66))))
        }
    }

    function cQ(a) {
        a && o == a && (cB(k[a][7]) && cu(k[a][7]), o != bN(31) && cv(W), o != bY(bN(24), bO(18)) && cv(X), cB(W, bN(3)), cB(X, bL(1)), cv(T), cB(T, bJ(-3)), cv(S), cB(S, bM(2)), cv(V), cB(V, bJ(-3)), cv(U), cB(U, bO(4)), cg(i, b$(bK(-3), bR(bJ(82), 0, 2), bO(56))), o = null)
    }

    function cP(a) {
        var b = bJ(32);
        k[b] = [a[b_(bO(39), bO(37))] || bO(4), bL(1), bL(1), bO(4), bK(-4), bK(-4)], k[b][6] = !!k[b][0]
    }

    function cO(a, b) {
        if (!!a && !!bQ(b)) {
            var c, d, e;
            for (c = 0; c < bQ(b); c++) {
                if (b[c] == bL(9)) {
                    e = b_(bH(9), b[c], bK(134), bN(142)), d = parseInt(a[e]) || 0;
                    if (d > 0) {
                        e = parseInt(a[b_(e, bK(3))]) || 0;
                        if (e) {
                            e = parseInt(bS(b_(bY(bI(34), bL(20)), bJ(5), bK(14)))) || 0;
                            if (e && bP() - e < d * 60 * 1e3) continue
                        } else if (bk % (d + 1) != 1) continue
                    }
                }
                if (b[c] == bO(32) && bl) continue;
                k[b[c]] = [a[b_(bM(12), b[c], bM(35))] || bN(3), a[b_(bN(13), b[c], bO(8))] || bM(2), a[b_(bO(14), b[c], bI(32))] || bJ(-3), a[b_(bL(11), b[c], bL(54))] || bH(-1), a[b_(bM(12), b[c], bK(49), bK(212))] || bN(3), a[b_(bL(11), b[c], bI(51), bH(120))] || bO(4), !1, null, a[b_(bH(9), b[c], bH(33), bK(25))] || bN(3), a[b_(bJ(7), b[c], bN(35), bK(30))] || bM(2), a[b_(bN(13), b[c], bI(30), bO(33))], a[b_(bH(9), b[c], bL(33), bN(32), bJ(15))],
                    [], null, !1
                ], k[b[c]][6] = !!(k[b[c]][0] || k[b[c]][3] || k[b[c]][5]), k[b[c]][5] && (k[b[c]][7] = cb(k[b[c]][5]), cN(b[c], k[b[c]][7])), b[c] == bL(9) && !bf && bz && x.handle(bF[10]) == bK(8) && (bm ? (cg(i, b$(bM(3), bY(bN(43), bO(126)))), cf(i, b$(bN(4), bY(bL(41), bJ(81)))), x.handle(bF[11])) : bl || (cu(W), W = cM(null, 160), cn(W, bO(10), function(a) {
                    cs(a) == W && (cq(a), x.handle(bF[11]))
                }), cx(B, W), cw(W)))
            }
        }
    }

    function cN(a, b) {
        var c, d;
        if (!!a && !!b) {
            c = ch(b, b$(bI(3), bL(63), bH(94), bO(32)));
            for (d = 0; d < bQ(c); d++) cn(c[d], bH(5), function(a) {
                W ? x.handle(bF[11]) : X ? x.handle(bF[23]) : x.toggle(), cq(a)
            });
            c = ch(b, b$(bL(6), bN(65), bI(93), bO(36)));
            for (d = 0; d < bQ(c); d++) cn(c[d], bN(9), function(b) {
                a == bL(9) ? (br = !0, x.handle(bF[12])) : a == bN(24) ? x.handle(bF[15]) : a == bY(bK(17), bK(10)) && (bs = !0, x.handle(bF[24])), cq(b)
            });
            c = cd(b, bR(bK(35), 5, 6));
            for (d = 0; d < bQ(c); d++) !ce(c[d], b$(bN(8), bL(63), bL(96), bL(29))) && !ce(c[d], b$(bO(9), bN(65), bN(98), bJ(29))) && (cn(c[d], bJ(3), function() {
                try {
                    bT(function() {
                        x.handle(bF[16], a)
                    }, 0)
                } catch (b) {}
            }), c[d][bN(52)] || (c[d][bL(50)] = b_(bO(4), ba ? bN(76) : bJ(48))))
        }
    }

    function cM(a, b) {
        var c = ct(bK(19), a, [bL(21), bN(29), bK(7), 0, bH(14), 0, bI(35), 0, bL(62), 0, bK(167), bK(79)]);
        b > 0 && cm(c, [bY(bH(118), ca(bM(122))), b]), a || cm(c, [bO(176), bI(213)]), cn(c, bY(bL(45), bI(92)), function(a) {
            a.stopPropagation()
        }), cv(c);
        return c
    }

    function cL(a) {
        var b = e[bY(bR(bK(67), 2), bR(bN(85), 9), bR(bJ(30), 2), bR(bI(69), 0, 1))],
            c = bM(2),
            d, f = [],
            g = bL(1),
            h, j;
        if (b) {
            c = bR(b, 0, 10), d = bR(z.conf[bY(bR(bK(26), 4, 5), bR(bJ(8), 1, 2), bR(bM(76), 3, 4))], 1), f.push(bR(c, d.length - c.length) + c), f.push(c + bR(c, 0, d.length - c.length)), g = bH(-1);
            for (j = 0; j < f.length; j++) {
                g = bH(-1);
                for (h = 0; h < d.length; h++) {
                    var l = parseInt(bR(d, h, h + 1)) + parseInt(bR(f[j], h, h + 1));
                    l >= 10 && (l -= 10), g += bL(1) + l
                }
                d = g
            }
        }(!b || b != c + g || parseInt(c) < z[bY(bR(bJ(44), 4, 6), bR(bJ(133), 0, 2))]) && k[a] && k[a][12] && k[a][6] && (k[a][6] = k[a][1] && k[a][1].indexOf(bY(bR(bH(29), 4, 5), bR(bK(7), 1, 2), bR(bM(60), 0, 2), bR(bK(133), 0, 2), bR(bI(24), 6, 8))) > 0, !k[a][6] && a == bH(7) && (cu(W), W = null), !k[a][6] && a == bY(bO(25), bN(17)) && (cu(X), cg(i, b$(bO(5), bM(15), bJ(49))), X = null))
    }

    function cK() {
        bp && z && (cf(i, b$(bO(5), bK(166))), z.load({
            sources: [{
                type: bp[1],
                src: bp[0]
            }]
        }))
    }

    function cJ() {
        var a, b;
        if (v && bQ(v) > 1) {
            N || (N = ct(bR(bK(35), 7), b$(bJ(0), bH(59))), cz(I, N), cn(N, bM(8), function(a) {
                try {
                    if (v && bQ(v) > 1) {
                        var b = cs(a),
                            c;
                        if (b && b.hasAttribute(b$(bO(95), bL(80)))) {
                            cq(a), c = parseInt(b.getAttribute(b$(bK(87), bM(81))));
                            if (c && bQ(v) >= c) {
                                w = v[c - 1];
                                if (x.handle(bF[7], w[2]) != bK(8)) {
                                    if (!w[5]) {
                                        for (bj = bQ(v) - 1; bj >= 0; bj--) v[bj][5] = !1;
                                        w[5] = !0, bp = w, bq = Math.floor(z[bJ(13)][bO(22)]), z.playing ? (bh = !0, z.pause(cK)) : cK(), cJ()
                                    }
                                } else z && z.playing && z.pause()
                            }
                            cg(i, b$(bO(5), bN(63), bM(74)))
                        }
                    }
                } catch (d) {}
            })), cg(N, b$(bN(4), bH(117))), O || (O = ct(bN(26), b$(bH(2), bH(59), bH(167))), cx(N, O)), cB(O, bL(1));
            for (bj = bQ(v) - 1; bj >= 0; bj--) w = v[bj], a = ct(bN(26), b$(bH(2), bL(61), bM(170), bJ(166))), b = ct(bR(bN(42), 3, 4)), cB(b, w[2]), w[3] ? (b[bH(40)] = w[0], b[bH(48)] = b_(bM(2), ba ? bJ(70) : bO(55)), cn(b, bI(4), function(a) {
                try {
                    z && z.playing && z.pause(), cg(i, b$(bK(-3), bM(62), bH(71))), x.handle(bF[7], cB(cs(a))) == bO(16) && cq(a)
                } catch (b) {}
            })) : b.setAttribute(b$(bI(89), bI(77)), bY(bO(4), bj + 1)), w[4] && cf(a, b$(bI(-1), bK(114))), w[5] && cf(a, b$(bI(-1), bN(120))), w[4] && w[5] && cf(N, b$(bN(4), bM(120))), cx(a, b), cx(O, a)
        } else N && (cu(N), N = O = null)
    }

    function cI(a) {
        cg(i, b$(bO(5), bN(74), bK(84))), a > 0 ? a < .25 ? cf(i, b$(bM(3), bH(70), 25)) : a < .5 ? cf(i, b$(bL(2), bL(72), 50)) : a < .75 && cf(i, b$(bM(3), bN(74), 75)) : cf(i, b$(bO(5), bO(75), 0)), cm(L, [bL(23), bY(parseInt(a * 100), bM(4))])
    }

    function cH(a) {
        v = [];
        if (!!a) {
            var b, c = b_(bM(18), bJ(1)),
                d, e, f, g = !1,
                h = parseInt(a[b_(bO(171), bJ(210))]) || 1,
                i, j;
            f = bS(b_(bY(bM(38), bI(17)), bI(115), bN(82))), a[b_(bL(33), bL(118), bI(77))] == bM(14) && (f = null), a[bH(57)] && (i = bP(), bx && (j = bS(bY(bU(), bJ(111), bK(54))), j && i - j < 36e5 && (i = j), sessionStorage.setItem(bY(bU(), bJ(111), bN(61)), i)));
            for (b = 0; b <= 7; b++) b > 0 && (c = b_(bI(14), bM(137), bK(0)), b > 1 && (c += b)), a[c] && (d = a[c], e = [d, d.toLowerCase().indexOf(bY(bO(63), bI(79))) > 0 ? bY(bN(19), bN(30), bN(96)) : bY(bL(17), bJ(24), bH(89)), a[c + b_(bI(-2), bI(27))] || bI(-2), a[c + b_(bO(4), bM(216))] || 0, a[c + b_(bJ(-3), bI(116))] || 0, f ? f == a[c + b_(bH(-1), bI(27))] : !1], i && (e[0] = bY(d, d.indexOf(bH(47)) >= 0 ? bM(52) : bL(49), bI(56), bH(42), i)), v.push(e), e[5] && (g = !0, e[3] && (e[5] = !1, g = !1)));
            !g && bQ(v) > 0 && (h > bQ(v) && (h = 1), v[h - 1][5] = !0)
        }
    }

    function cG(a) {
        function d(a) {
            a = parseInt(a, 10);
            return a >= 10 ? a : bY(0, a)
        }
        a = a || 0;
        var b = Math.floor(a / 3600),
            c = Math.floor(a / 60);
        a = a - c * 60;
        if (b >= 1) {
            c -= b * 60;
            return bY(b, bK(76), d(c), bN(83), d(a))
        }
        return bY(d(c), bJ(77), d(a))
    }

    function cF(a, b, c) {
        if (!!a && !!b) {
            var d = cj(b),
                e, f, g;
            a.width && a.height ? (e = [a.width, a.height], a.tagName && a.tagName.toLowerCase() == bM(72) && (e = cj(a))) : e = cj(a), c == bK(111) ? cm(a, [bH(19), bO(30), bM(13), bY(50, bJ(-1)), bM(17), bY(50, bK(-2)), bH(37), 0, bY(bO(42), ca(bH(10))), bY(-e[0] / 2, bO(13)), bY(bH(37), ca(bN(18))), bY(-e[1] / 2, bN(12))]) : c == bJ(113) ? e[0] / e[1] > d[0] / d[1] ? (f = e[1] * d[0] / e[0] / d[1] * 100, cm(a, [bM(22), bK(22), bO(15), 0, bI(13), bY((100 - f) / 2, bO(6)), bK(13), bY(100, bM(4)), bJ(19), bY(f, bK(-2)), bL(39), 0])) : (g = e[0] * d[1] / e[1] / d[0] * 100, cm(a, [bJ(17), bL(27), bH(10), bY((100 - g) / 2, bK(-2)), bN(18), 0, bI(15), bY(g, bL(3)), bK(18), bY(100, bK(-2)), bN(41), 0])) : cm(a, [bO(24), bL(27), bI(9), 0, bJ(12), 0, bJ(14), bY(100, bN(5)), bI(20), bY(100, bK(-2)), bO(42), 0])
        }
    }

    function cE(a) {
        if (a) return a[bY(bO(118), bK(162))];
        return bN(3)
    }

    function cD(a) {
        var b = {},
            c, d, e = bJ(-3),
            f = !1,
            g = 0,
            h = 0;
        if (!a) return b;
        if (a.nodeType == 1) {
            if (bQ(a.attributes) > 0) {
                b[bY(bH(55), bL(58))] = {};
                for (h = 0; h < bQ(a.attributes); h++) {
                    var i = a.attributes.item(h);
                    b[bY(bK(52), bJ(54))][i.nodeName.toLowerCase()] = i.nodeValue
                }
            }
        } else if (a.nodeType == 3 || a.nodeType == 4) return a.nodeValue;
        if (a.hasChildNodes()) {
            for (g = 0; g < bQ(a.childNodes); g++) {
                c = a.childNodes.item(g), d = c.nodeName.toLowerCase();
                if (c.nodeType == 3 || c.nodeType == 4) e += (c.nodeValue || bH(-1)).trim();
                else {
                    f = !0;
                    if (typeof b[d] == bO(115)) b[d] = cD(c);
                    else {
                        if (typeof b[d].push == bM(113)) {
                            var j = b[d];
                            b[d] = [], b[d].push(j)
                        }
                        b[d].push(cD(c))
                    }
                }
            }
            f || (b[bY(bL(115), bN(169))] = e)
        }
        return b
    }

    function cC(a) {
        if (!bQ(a)) return {};
        var b = 0,
            c = {};
        while (b < bQ(a)) b <= bQ(a) && (c[a[b]] = a[++b], b++);
        return c
    }

    function cB(a, b) {
        if (!a) return bK(-4);
        typeof b != bH(110) && (a.innerHTML = b);
        return a.innerHTML
    }

    function cA(a, b) {
        !!a && !!b && a.parentNode.insertBefore(b, a)
    }

    function cz(a, b) {
        !!a && !!b && a.parentNode.insertBefore(b, a.nextSibling)
    }

    function cy(a, b) {
        !!a && !!b && b.parentNode != a && (a.firstChild ? a.insertBefore(b, a.firstChild) : a.appendChild(b))
    }

    function cx(a, b) {
        !!a && !!b && b.parentNode != a && a.appendChild(b)
    }

    function cw(a) {
        !a || cm(a, [bN(48), bM(32)])
    }

    function cv(a) {
        !a || cm(a, [bL(46), bL(90)])
    }

    function cu(a) {
        !!a && !!a.parentNode && a.parentNode.removeChild(a)
    }

    function ct(a, b, c) {
        var d = g.createElement((a ? a : bL(24)).toUpperCase());
        b && (d[bY(bM(70), ca(bI(67)))] = b), cm(d, c);
        return d
    }

    function cs(a) {
        a = a || window.event;
        return a.srcElement || a.target
    }

    function cr(a, b) {
        a = a || window.event;
        var c = a.clientX || (a.changedTouches ? a.changedTouches[0].clientX : 0),
            d = a.clientY || (a.changedTouches ? a.changedTouches[0].clientY : 0),
            e;
        if (b) {
            e = b.getBoundingClientRect();
            return [c - e.left, d - e.top]
        }
        return [c, d]
    }

    function cq(a) {
        a = a || window.event, a.preventDefault ? a.preventDefault() : a.returnValue = !1;
        return !1
    }

    function cp(a, b, c) {
        if (!!a)
            for (var d = 0; d < bQ(b); d++) cn(a, b[d], c)
    }

    function co(a, b, c) {
        !a || !b || !c || (a.removeEventListener ? a.removeEventListener(b, c, !1) : a.detachEvent && a.detachEvent(bH(112) + b, c))
    }

    function cn(a, b, c) {
        !a || !b || !c || (a.addEventListener ? a.addEventListener(b, c, !1) : a.attachEvent ? a.attachEvent(bK(109) + b, c) : a[bH(112) + b] = c, a == g && h.push([b, c]))
    }

    function cm(a, b) {
        if (!!a && !!bQ(b)) {
            var c = 0,
                d = {};
            while (c < bQ(b)) c <= bQ(b) && (d[b[c]] = b[++c], c++);
            cl(a, d)
        }
    }

    function cl(a, b) {
        if (!!a && !!b)
            for (var c in b) a.style[c] = b[c]
    }

    function ck(a) {
        if (!a) return [0, 0];
        var b = 0,
            c = 0;
        if (a && a.offsetParent)
            while (a) b += a.offsetLeft, c += a.offsetTop, a = a.offsetParent;
        return [b, c]
    }

    function cj(a) {
        if (!a) return [0, 0];
        return [a.offsetWidth, a.offsetHeight]
    }

    function ci(a, b) {
        return cc(ch(a, b))
    }

    function ch(a, b) {
        var c = [],
            d = !0,
            e, f;
        if (!a || !b) return c;
        b.indexOf(bN(91)) > 0 && (b = bR(b, 0, b.indexOf(bL(89))), d = !1), e = cd(a, bJ(85));
        for (f = 0; f < bQ(e); f++) ce(e[f], b, !d) && c.push(e[f]);
        return c
    }

    function cg(a, b) {
        if (!!a) {
            var c = a[bY(bH(67), ca(bH(68)))].split(bN(70)),
                d = bN(3),
                e = !1,
                f;
            b.indexOf(bI(86)) > 0 && (b = bR(b, 0, b.indexOf(bL(89))), e = !0);
            for (f = 0; f < bQ(c); f++) e && c[f].indexOf(b) !== 0 ? d += bK(63) + c[f] : !e && c[f] != b && (d += bI(65) + c[f]);
            a[bY(bI(66), ca(bN(72)))] = d.trim()
        }
    }

    function cf(a, b) {
        !a || ce(a, b, !1) || (bQ(a[bY(bL(69), ca(bL(70)))]) == 0 ? a[bY(bH(67), ca(bK(65)))] = b : a[bY(bH(67), ca(bI(67)))] += bL(68) + b)
    }

    function ce(a, b, c) {
        if (!a) return !1;
        var d = a[bY(bM(70), ca(bO(73)))].split(bJ(64)),
            e;
        for (e = 0; e < bQ(d); e++) {
            if (c && d[e].indexOf(b) === 0) return !0;
            if (d[e] == b) return !0
        }
        return !1
    }

    function cd(a, b) {
        if (!a || !b) return [];
        return a.getElementsByTagName(b)
    }

    function cc(a) {
        if (bQ(a) > 0) return a[0];
        return null
    }

    function cb(a, b) {
        b || (b = g);
        return b.getElementById(a)
    }

    function ca() {
        var a = Array.prototype.slice.call(arguments),
            b;
        for (b = 0; b < bQ(a); b++) a[b] = a[b].charAt(0).toUpperCase() + a[b].slice(1);
        return a.join(bJ(-3))
    }

    function b_() {
        var a = Array.prototype.slice.call(arguments);
        return a.join(bK(208))
    }

    function b$() {
        var a = Array.prototype.slice.call(arguments);
        return a.join(bO(215))
    }

    function bZ() {
        var a = Array.prototype.slice.call(arguments);
        return a.join(bN(70))
    }

    function bY() {
        var a = Array.prototype.slice.call(arguments);
        return a.join(bK(-4))
    }

    function bX(a, b, c) {
        if (!!a && !!b) {
            var d = new XMLHttpRequest;
            d.withCredentials = !0, d.onreadystatechange = function() {
                this.readyState === 4 && (this.status >= 400 ? c && c() : b(this.responseXML))
            }, d.open(bK(108), a, !0), d.send()
        }
    }

    function bW(a, b, c) {
        if (!!a && !!b) {
            var d = new XMLHttpRequest;
            d.onreadystatechange = function() {
                this.readyState === 4 && (this.status >= 400 ? c && c() : b(this.responseText))
            }, d.open(bL(113), a, !0), d.send()
        }
    }

    function bV(a) {
        a && ((new Image).src = a)
    }

    function bU(a) {
        a && (window.top ? window.top.location = a : window.location = a);
        return window.location
    }

    function bT(a, b) {
        a && setTimeout(a, b)
    }

    function bS(a) {
        var b = null;
        try {
            bx && (b = sessionStorage.getItem(a), b || (b = localStorage.getItem(a)))
        } catch (c) {}
        b || (b = bH(-1));
        return b
    }

    function bR(a, b, c) {
        if (a) return a.substring(b, c);
        return bJ(-3)
    }

    function bQ(a) {
        if (a) return a.length;
        return 0
    }

    function bP() {
        return (new Date).getTime()
    }

    function bO(a) {
        return f[a - 4]
    }

    function bN(a) {
        return f[a - 3]
    }

    function bM(a) {
        return f[a - 2]
    }

    function bL(a) {
        return f[a - 1]
    }

    function bK(a) {
        return f[a + 4]
    }

    function bJ(a) {
        return f[a + 3]
    }

    function bI(a) {
        return f[a + 2]
    }

    function bH(a) {
        return f[a + 1]
    }
    var f = ["", "is", "%", "fp", "url", "kt", "click", "type", "pre", "px", "adv", "left", "true", "ad", "pause", "top", "video", "width", "time", "player", "position", "post", "height", "div", "logo", "volume", "absolute", "/", "start", "text", "block", "vast", "skip", "src", "duration", "related", "kvs", "bottom", "margin", "metadata", "mouse", "href", "timeline", "=", "touch", "display", "error", "function", "?", "target", "&", "blank", "visible", "html", "screen", "roll", "@", "attributes", "rnd", ".", "settings", "right", "api", "playing", "embed", "hide", "progress", " ", "class", "name", "iframe", "vol", "open", "parent", "play", "ui", "popunder", "false", "paused", "format", ":", "flv", "controlbar", "hidden", "over", "adzone", "load", "full", "*", "none", "mp4", "data", "loaded", "flash", "end", "btn", "content", "mute", "visibility", "float", "css", "auto", "fullscreen", "m", "preload", "subtitles", "preview", "stream", "started", "finished", "muted", "undefined", "get", "on", "#", "preserve", "match", "selected", "hd", "z", "index", "id", "out", "resume", "img", "catch", "transition", "string", "ads", "waiting", "fade", "down", "protect", "before", "advertising", "alt", "media", "elapsed", "replay", "after", "window", "document", "no", "referer", "code", "creative", "tracking", "exit", "tooltip", "brand", "clip", "autoplay", "poster", "screens", "native", "ready", "seek", "stop", "engine", "controls", "move", "link", "sec", "em", "skin", "test", "value", "default", "list", "item", "loading", "overflow", "background", "expand", "collapse", "unload", "playsinline", "fill", "focus", "body", "scroll", "inline", "linear", "offset", "event", "quartile", "file", "in", "cuepoint", "swf", "lang", "splash", "loop", "cuepoints", "finish", "level", "vertical", "dragging", "location", "stopped", "scrolled", "changing", "activated", "deactivated", "show", "changed", "[", "object", "array", "]", "storage", "-", "_", "slot", "redirect", "transparent", "adaptive", "container", "webkit", "change", "frame", "border", "scrolling", "domain", "title", "impression", "through", "view", "first", "mid", "point", "third", "complete", "urls", "same", "relative", "context", "menu", "textarea", "ratio", "license", "key", "kind", "en", "thumbnails", "interval", "image", "blur", "help", "a", "anchor", "disable", "resize", "fixed", "style", "slider", "up", "only", "track", "close", "flow", "http", "white", "dark", "rel", "stylesheet", "head"],
        g = document,
        h = [],
        i = cb(a),
        j = {},
        k = {},
        l = {},
        m, n, o, p, q, r = {},
        s, t, u, v = [],
        w, x, y, z, A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z, $, _, ba, bb, bc, bd, be, bf, bg, bh, bi, bj, bk, bl, bm, bn, bo, bp, bq = 0,
        br, bs, bt, bu, bv = window[bK(194)],
        bw = !0,
        bx = !1,
        by = flowplayer.support.autoplay,
        bz = flowplayer.support.inlineVideo,
        bA = flowplayer.support.flashVideo,
        bB = flowplayer.support.touch,
        bC = flowplayer.support.volume,
        bD = !1,
        bE = !1,
        bF = [bY(bH(4), ca(bI(17)), ca(bI(90))), bY(bK(1), ca(bL(17)), ca(bJ(105))), bY(bI(3), ca(bK(12)), ca(bN(81))), bY(bO(9), ca(bK(12)), ca(bK(195))), bY(bH(4), ca(bH(15)), ca(bO(204))), bY(bI(3), ca(bM(18)), ca(bO(70))), bY(bH(4), ca(bM(18)), ca(bK(105))), bY(bO(9), ca(bK(12)), ca(bJ(76)), ca(bK(197))), bY(bK(1), ca(bK(83)), ca(bK(50)), ca(bI(200))), bY(bI(3), ca(bH(86)), ca(bN(57)), ca(bO(207))), bY(bJ(2), ca(bO(12)), ca(bL(56)), ca(bI(131))), bY(bJ(2), ca(bI(6)), ca(bN(58)), ca(bL(109))), bY(bJ(2), ca(bO(12)), ca(bH(54)), ca(bL(110))), bY(bH(4), ca(bM(23)), ca(bM(57)), ca(bI(131))), bY(bO(9), ca(bJ(18)), ca(bI(53)), ca(bI(106))), bY(bK(1), ca(bO(25)), ca(bL(56)), ca(bO(113))), bY(bK(1), ca(bM(136)), ca(bK(2))), bY(bH(4), ca(bM(136)), ca(bO(208))), bY(bJ(2), ca(bJ(22)), ca(bN(208))), bY(bK(1), ca(bI(23)), ca(bJ(107))), bY(bJ(2), ca(bO(35)), ca(bL(93))), bY(bO(9), ca(bH(30)), ca(bJ(43))), bY(bN(8), ca(bY(bI(19), bO(18))), ca(bM(57)), ca(bH(132))), bY(bJ(2), ca(bY(bK(17), bO(18))), ca(bK(51)), ca(bO(112))), bY(bI(3), ca(bY(bN(24), bO(18))), ca(bM(57)), ca(bK(105)))];
    typeof String.prototype.trim != bL(48) && (String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, bL(1))
    }), Array.isArray || (Array.isArray = function(a) {
        return Object.prototype.toString.call(a) === bZ(bY(bK(202), bM(209)), bY(ca(bJ(205)), bL(210)))
    });
    if (typeof Storage !== bO(115)) try {
        localStorage.setItem(b_(bY(bN(39), bK(15)), bO(169), bO(214)), bO(169)), bx = !0
    } catch (bG) {}
    cB(i, bH(-1)), e[bM(66)] == 0 && (e[bN(67)] = bO(81)), e[bL(65)] == 1 && (e[bH(63)] = bL(13));
    for (s in e) typeof e[s] != bH(126) && (e[s] = bY(bO(4), e[s]));
    if (!this[bY(bN(39), bK(15))]) {
        s = e[bN(167)];
        if (!s || s.indexOf(bM(263)) < 0 || s.indexOf(bY(bL(28), bH(26))) < 0)
            if (b) {
                if (!s || s.indexOf(bY(bN(62), bI(98))) <= 0) e[bJ(161)] == 2 ? s = bY(bN(265), bI(57), bM(102)) : s = bY(bN(266), bK(55), bO(104));
                s = bY(bR(b, 0, b.lastIndexOf(bH(26))), bH(26), bK(160), bL(28), s)
            } A = ct(bJ(158)), A.setAttribute(bN(267), bL(266)), A.setAttribute(bM(9), bY(bH(28), bL(28), bO(104))), A.setAttribute(bL(42), s), cx(cc(cd(g, bI(264))), A)
    }
    e[b_(bK(128), bK(26))] && (y = ct(bJ(67)), cn(y, bJ(83), function() {
        bE = !0
    }), y[bH(21)] = bY(1, bL(10)), y[bJ(14)] = bY(1, bJ(6)), y[bO(125)] = b_(bO(132), bH(69)), y[bY(bH(67), ca(bH(68)))] = b$(bM(130), bJ(67)), y[bK(29)] = bY(e[b_(bH(131), bJ(27))], bI(46), b_(bJ(131), bL(122)), bN(46), Math.random(), bH(49), b_(bH(84)), bJ(40), Math.random()), cm(y, [bL(21), bM(28), bI(9), bY(-10, bI(7)), bI(13), bY(-10, bJ(6))]), cx(i, y)), x = {
        container: function() {
            return i
        },
        listen: function(a, b) {
            if (!a || !b || typeof b != bN(50)) return this;
            this.a || (this.a = {}), this.a[a] || (this.a[a] = []), this.a[a].push(b);
            return this
        },
        handler: function(a) {
            if (!a || typeof a != bJ(44)) return this;
            this.b || (this.b = []), this.b.push(a);
            return this
        },
        handle: function(a, b) {
            var c, d, e, f;
            if (this.b)
                for (d = 0; d < bQ(this.b); d++) try {
                    f = this.b[d](a, b), f && (e = f)
                } catch (g) {}
            if (this.a) {
                c = this.a[a];
                if (c)
                    for (d = 0; d < bQ(c); d++) try {
                        f = c[d](b), f && (e = f)
                    } catch (g) {}
            }
            return e
        },
        play: function(a) {
            z && (a ? (typeof a == bI(125) && (v = [
                [a, a.indexOf(bY(bI(57), bO(85))) > 0 ? bY(bO(20), bJ(24), bK(89)) : bY(bL(17), bJ(24), bM(92)), bL(1), 0, 0, !0]
            ], bp = v[0]), z.playing ? z.pause(cK()) : cK(), cJ()) : z.ready ? z.play() : z.load())
        },
        pause: function() {
            z && z.pause()
        },
        toggle: function() {
            z && (cg(i, b$(bM(3), bL(79))), z.toggle())
        },
        skip_preroll: function() {
            this.handle(bF[12])
        },
        skip_postroll: function() {
            this.handle(bF[15])
        },
        fullscreen: function() {
            z && z.fullscreen()
        },
        volume: function(a) {
            if (z) {
                a && z.volume(a);
                return z.volumeLevel
            }
            return 1
        },
        mute: function(a) {
            z && z.mute(a)
        },
        muted: function() {
            if (z) return z.muted;
            return !1
        },
        unload: function() {
            if (z) {
                var a = ci(i, b$(bI(1), bI(156)));
                a && a.canPlayType && (a.autoplay = !1, a.preload = bM(91), a.src = null), z.disable(), z.shutdown()
            }
            if (bQ(h))
                for (var b = 0; b < bQ(h); b++) co(g, h[b][0], h[b][1]);
            cw(T)
        },
        fpapi: function() {
            return z
        }
    }, x.conf = e, this[bY(bH(35), bL(20))] || (this[bY(bL(37), bL(20))] = {}), this[bY(bN(39), bJ(16))][a] && this[bY(bJ(33), bO(23))][a].unload(), this[bY(bO(40), bO(23))][a] = x, i && bT(c_, 0);

    return x
}
