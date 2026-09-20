import { useState, type FormEvent } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import axios from 'axios';

export default function Register() {
  const { register } = useAuth();
  const navigate = useNavigate();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [phone, setPhone] = useState('');
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    try {
      await register({
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
        phone,
      });
      navigate('/');
    } catch (err) {
      if (axios.isAxiosError(err)) {
        setError(err.response?.data?.message ?? 'Hiba történt.');
      }
    }
  };

  return (
    <div className="h-full flex flex-col bg-bg px-6 pt-10 pb-8 font-sans">
      <div className="font-mono font-extrabold text-[13px] tracking-[0.14em] text-ink mb-8">
        GEAR<span className="text-accent">·</span>TOSHARE
      </div>

      <h1 className="text-[26px] font-extrabold leading-[1.15] tracking-tight text-ink mb-2">
        Fiók létrehozása
      </h1>
      <p className="text-sm leading-relaxed text-secondary mb-5">
        Két perc, és bérelhetsz vagy bérbe adhatsz.
      </p>

      <form onSubmit={handleSubmit} className="flex flex-col gap-2.5 mb-5">
        <div>
          <label className="block font-mono text-[10px] tracking-[0.08em] text-[#8b877f] mb-1.5">TELJES NÉV</label>
          <input
            value={name}
            onChange={(e) => setName(e.target.value)}
            placeholder="Németh Tamás"
            required
            className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] bg-white text-sm text-ink placeholder:text-[#8b877f] outline-none focus:border-ink"
          />
        </div>
        <div>
          <label className="block font-mono text-[10px] tracking-[0.08em] text-[#8b877f] mb-1.5">E-MAIL</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="nev@example.com"
            required
            className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] bg-white text-sm text-ink placeholder:text-[#8b877f] outline-none focus:border-ink"
          />
        </div>
        <div>
          <label className="block font-mono text-[10px] tracking-[0.08em] text-[#8b877f] mb-1.5">TELEFON</label>
          <input
            value={phone}
            onChange={(e) => setPhone(e.target.value)}
            placeholder="+36301234567"
            required
            className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] bg-white text-sm text-ink placeholder:text-[#8b877f] outline-none focus:border-ink"
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
            className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] bg-white text-sm text-ink outline-none focus:border-ink"
          />
        </div>
        <div>
          <label className="block font-mono text-[10px] tracking-[0.08em] text-[#8b877f] mb-1.5">JELSZÓ MÉGEGYSZER</label>
          <input
            type="password"
            value={passwordConfirmation}
            onChange={(e) => setPasswordConfirmation(e.target.value)}
            placeholder="••••••••••"
            required
            className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] bg-white text-sm text-ink outline-none focus:border-ink"
          />
        </div>

        <button
          type="submit"
          className="w-full py-4 rounded-xl bg-accent text-ink font-bold text-[15.5px] mt-3 hover:bg-[#c28a1d] transition-colors"
        >
          Fiók létrehozása
        </button>

        {error && <p className="text-danger text-sm text-center">{error}</p>}
      </form>

      <div className="flex-1" />

      <div className="text-center text-[13.5px] text-secondary">
        Van már fiókod?{' '}
        <Link to="/login" className="font-semibold text-[#a97416]">
          Bejelentkezés
        </Link>
      </div>
    </div>
  );
}