import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import axios from 'axios';

export default function Login() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    try {
      await login({ email, password });
      navigate('/');
    } catch (err) {
      if (axios.isAxiosError(err)) {
        setError(err.response?.data?.message ?? 'Hiba történt.');
      }
    }
  };

  return (
    <div className="h-full flex flex-col bg-bg px-6 pt-10 pb-8 font-sans">
      <div className="font-mono font-extrabold text-[13px] tracking-[0.14em] text-ink mb-10">
        GEAR<span className="text-accent">·</span>TOSHARE
      </div>

      <h1 className="text-[26px] font-extrabold leading-[1.15] tracking-tight text-ink mb-2">
        Üdvözlünk újra
      </h1>
      <p className="text-sm leading-relaxed text-secondary mb-6">
        Jelentkezz be, és folytasd a bérlést vagy a bérbeadást.
      </p>

      <form onSubmit={handleSubmit} className="flex flex-col gap-3">
        <div>
          <label className="block font-mono text-[10px] tracking-[0.08em] text-[#8b877f] mb-1.5">E-MAIL</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="nev@example.com"
            required
            className="w-full px-4 py-3.5 rounded-xl border border-[#d6d2cb] bg-white text-[14.5px] text-ink placeholder:text-[#8b877f] outline-none focus:border-ink"
          />
        </div>
        <div>
          <label className="block font-mono text-[10px] tracking-[0.08em] text-[#8b877f] mb-1.5">JELSZÓ</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="••••••••••"
            required
            className="w-full px-4 py-3.5 rounded-xl border border-[#d6d2cb] bg-white text-[14.5px] text-ink outline-none focus:border-ink"
          />
        </div>

        <button
          type="submit"
          className="w-full py-4 rounded-xl bg-accent text-ink font-bold text-[15.5px] mt-2 hover:bg-[#c28a1d] transition-colors"
        >
          Bejelentkezés
        </button>

        {error && <p className="text-danger text-sm text-center">{error}</p>}
      </form>

      <div className="flex-1" />

      <div className="text-center text-[13.5px] text-secondary">
        Nincs még fiókod?{' '}
        <Link to="/register" className="font-semibold text-[#a97416]">
          Regisztrálj
        </Link>
      </div>
    </div>
  );
}