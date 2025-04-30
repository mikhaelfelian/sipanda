<?php
namespace App\Libraries;

class NaiveBayesAnalyzer
{
    // Dummy word lists for demonstration
    protected $positiveWords = [
        'baik', 'bagus', 'cerdas', 'cerah', 'sejahtera', 'aman', 'stabil', 'berhasil', 'sukses', 'prestasi',
        'kemajuan', 'unggul', 'terbaik', 'positif', 'harapan', 'semangat', 'menang', 'terpercaya', 'adil', 'damai',
        'maju', 'inovasi', 'solusi', 'pertumbuhan', 'berkembang', 'produktif', 'efisien', 'cepat', 'hemat', 'hebat',
        'berkualitas', 'tinggi', 'pujian', 'kemakmuran', 'berdaya', 'tangguh', 'kuat', 'berhasil', 'santun', 'mulia',
        'profesional', 'dedikasi', 'berbakti', 'menghargai', 'terampil', 'terpadu', 'gotong royong', 'kompak', 'solid',
        'inspiratif', 'strategis', 'cerdas', 'inovatif', 'modern', 'kompeten', 'berintegritas', 'disiplin', 'jujur',
        'setia', 'peduli', 'empati', 'responsif', 'ramah', 'beruntung', 'rezeki', 'karunia', 'anugerah', 'bangkit',
        'aktif', 'gigih', 'optimis', 'peningkatan', 'terpenuhi', 'kepuasan', 'kesejahteraan', 'keamanan', 'penyelesaian',
        'keadilan', 'kepercayaan', 'penemuan', 'kemudahan', 'peluang', 'solutif', 'efektif', 'transparan', 'akuntabel',
        'konsisten', 'inspirasi', 'kreatif', 'perbaikan', 'kemitraan', 'kerja sama', 'kesepakatan', 'tercapai',
        'peningkatan', 'berkah', 'nyaman', 'terjangkau', 'ceria', 'menyenangkan', 'sehat', 'makmur'
    ];
    
    protected $negativeWords = [
        'buruk', 'gagal', 'krisis', 'bencana', 'korupsi', 'pengangguran', 'kemiskinan', 'ketimpangan', 'konflik', 'rusuh',
        'kekerasan', 'perang', 'radikalisme', 'terorisme', 'pembunuhan', 'pelecehan', 'penipuan', 'kecurangan', 'penyimpangan', 'penyiksaan',
        'kecelakaan', 'kerusakan', 'kehancuran', 'kehancuran', 'mundur', 'melemah', 'lesu', 'lambat', 'defisit', 'inflasi',
        'penurunan', 'penyusutan', 'masalah', 'perselisihan', 'ketegangan', 'kesalahan', 'pelanggaran', 'pencemaran', 'penyakit', 'penganiayaan',
        'kemunduran', 'penindasan', 'pengkhianatan', 'penggelapan', 'kriminal', 'penjara', 'hukuman', 'putus asa', 'kekecewaan', 'putus',
        'negatif', 'tidak stabil', 'tidak aman', 'tidak adil', 'tidak layak', 'ancaman', 'terlantar', 'ketidakpastian', 'kegagalan', 'kesulitan',
        'kerugian', 'penurunan', 'kelangkaan', 'keterbatasan', 'keterlambatan', 'kemacetan', 'kebisingan', 'kotor', 'lapar', 'haus',
        'kemarahan', 'kebencian', 'dendam', 'cemburu', 'malas', 'panik', 'cemas', 'takut', 'gelisah', 'stres',
        'frustrasi', 'pahit', 'luka', 'pemborosan', 'keterpurukan', 'kacau', 'tegang', 'gelap', 'runtuh', 'terpuruk',
        'penghinaan', 'pengusiran', 'pengasingan', 'pemberontakan', 'penculikan', 'penindakan', 'ketergantungan', 'pengabaian', 'penghalangan', 'kecurigaan'
    ];
    
    public function analyzeSentiment($text)
    {
        $text = strtolower($text);
        $pos = 0; $neg = 0;
        foreach ($this->positiveWords as $word) {
            if (strpos($text, $word) !== false) $pos++;
        }
        foreach ($this->negativeWords as $word) {
            if (strpos($text, $word) !== false) $neg++;
        }
        if ($pos > $neg) return 'positive';
        if ($neg > $pos) return 'negative';
        return 'neutral';
    }

    public function predictViral($text)
    {
        // Simple heuristic: if text contains 'viral', 'trending', or is long, predict viral
        $viralWords = ['viral', 'trending', 'breaking', 'hot'];
        foreach ($viralWords as $word) {
            if (strpos(strtolower($text), $word) !== false) return true;
        }
        if (str_word_count($text) > 30) return true;
        return false;
    }
}
